
    class Animator {
        constructor() {
            //create raf function
            let raf = window.requestAnimationFrame ||
                window.webkitRequestAnimationFrame ||
                window.mozRequestAnimationFrame ||
                window.oRequestAnimationFrame ||
                window.msRequestAnimationFrame ||
                function (callback) {
                    window.setTimeout(callback, 1000 / 60);
                };
            //bind context window
            this.raf = raf.bind(window);

            let divTest = document.createElement("div");

            /* checking needed features */
            this.canAnimate = (typeof this.raf === "function") &&
                ("classList" in divTest) &&
                typeof divTest.style.transition !== undefined;

            /* requestAnimationFrame queue */
            this.frames = [];
            //storage for _finisherHandler() method
            this.finisher_show = false;
            this.finisher_hide = false;

            //storage for eventlistener functions
            this.handler = {};

            //animation in progress indicator
            this.in_progress = false;
        }

        insert(target, el, options = {}, before = null) {
            this._hide(el);
            target.insertBefore(el, before);
            this.show(el, options);
        }

        remove(el, options = {}) {
            options.systemDoneCallback = function () {
                el.parentNode.removeChild(el);
            }

            this.hide(el, options);
        }

        _reset() {
            if (this.frames.length > 0) {
                this.frames = [];
            }
            this._removeClasses(this.el, Object.values(this.settings.classNames));

            if (this.finisher_show) {
                this._finishHandler(this.finisher_show, true);
            }
            if (this.finisher_hide) {
                this._finishHandler(this.finisher_hide, true);
            }
        }

        show(el, options = {}) {
            this.el = el;
			let elf = el[0] || el;
            //skip animate if requestAnimationFrame is not supported
            if (!this.canAnimate) {
                return this._show(el);
            }

            /* merge defaults and users options */
            let settings = this._calcOptions(options);
            this.settings = settings;

            //reset to initial state
            this._reset();

            // return if element is NOT hidden
            if (!this._isHidden(elf) && !this.in_progress) {
                return;
            }

            /* set handler on animation finish */
            this.finisher_show = [el, settings.track, settings.duration, () => {
                this._removeClasses(el, settings.classNames.active);
                this.finisher_show = false;
                this.in_progress = false;
                settings.systemDoneCallback(el);
                settings.after(el);
            }
            ];

            this._addFrame(() => {
                this.in_progress = true;
                this._show(el);
                this._finishHandler(this.finisher_show);
                this._addClasses(el, [settings.classNames.active, settings.classNames.enter]);
                settings.before(el);
            });

            this._addFrame(() => {
                this._removeClasses(el, settings.classNames.enter);
                this._addClasses(el, settings.classNames.enterTo);
            });

            this._nextFrame();
        }

        hide(el, options = {}) {
            this.el = el;
            let elf = el[0] || el;
            if (!this.canAnimate) {
                return this._hide(el);
            }
			
            let settings = this._calcOptions(options);
            this.settings = settings;

            //~ this._reset();
            //return if element is hidden and animation not in progress
            if (this._isHidden(elf) && !this.in_progress) {
				//~ console.log('hidden');
				//~ console.log(this._isHidden(elf));
                return;
            }

            this.finisher_hide = [el, settings.track, settings.duration, () => {
                this._hide(el);
                this._removeClasses(el, [settings.classNames.enterTo, settings.classNames.active]);
                options.systemOnEnd && options.systemOnEnd();
                this.finisher_hide = false;
                this.in_progress = false;
                settings.systemDoneCallback(el);
                settings.after(el);
            }
            ];

            this._addFrame(() => {
                this.in_progress = true;
                this._finishHandler(this.finisher_hide);
                this._addClasses(el, settings.classNames.active);
                settings.before(el);
            });

            this._addFrame(() => {
                this._removeClasses(el, settings.classNames.enterTo);
                this._addClasses(el, settings.classNames.enter);
            });

            this._nextFrame();
        }

        _finishHandler(arr, abort) {
            let el = arr[0][0] || arr[0], track = arr[1], duration = arr[2], fn = arr[3], eventName, isCssTrack = true;

            if (track === 'transition') {
                eventName = 'transitionend';
            } else if (track === 'animation') {
                eventName = 'animationend';
            } else {
                isCssTrack = false;
            }
			
			
			
            if (abort) {
                el.removeEventListener(eventName, this.handler[eventName]);
                return;
            }

            this.handler[eventName] = () => {
                el.removeEventListener(eventName, this.handler[eventName]);
                fn();
            };

            if (isCssTrack) {
                el.addEventListener(eventName, this.handler[eventName]);
            } else {
                setTimeout(fn, duration);
            }
        }

        _calcOptions(options) {
            let classNames = this._mergeSettings(this._classNames(options.name), options.classNames);
			//~ console.log(classNames);
            delete options.classNames;

            let defaults = {
                name: '',
                track: 'transition',
                duration: null,
                classNames: classNames,
                before(el){
                },
                after(el){
                },
                systemDoneCallback(el){
                }
            }
            return this._mergeSettings(defaults, options);
        }

        _classNames(name) {
            return !name ? {
				enter: '',
				enterTo: '',
				active: ''
			} 
            : {
                enter: name + '-enter',
                enterTo: name + '-enterTo',
                active: name + '-active'
            }
        }

        _addFrame(fn) {
            this.frames.push(fn);
        }

        _nextFrame() {
            if (this.frames.length === 0) {
                return;
            }

            let frame = this.frames.shift();

            frame();
            //we use double raf because of a bug https://bugs.chromium.org/p/chromium/issues/detail?id=675795
            this.raf(() => {
                this.raf(() => {
                    this._nextFrame();
                });
            });

        }

        _addClasses(el, str) {
            this._classList('add', el, str);
        }

        _removeClasses(el, str) {
            this._classList('remove', el, str);
        }

        _classList(action, el, str) {
            if (typeof str === 'string') {
                str = str.split(' ');
            }
            
            if(!el.constructor.name.match(/HTML.+?Element/)){
				for(let i = 0; i < el.length; i++){
					this._classList(action, el[i], str);
				}
				return;
			}
			            
            for (let i = 0; i < str.length; i++) {
				if(typeof str[i] === 'object'){
					this._classList(action, el, str[i]);
				}else if(str[i] !== ''){
					el.classList[action](str[i]);
				}
            }
        }

        _mergeSettings(def, extra) {
            return typeof extra !== "object" ? def : Object.assign(def, extra);
        }

        _hide(el) {
            if(el.constructor.name.match(/HTML.+?Element/)){
				el.style.display = 'none';
			}else{
				for(let i = 0; i < el.length; i++){
					this._hide(el[i]);
				}
			}
        }

        _show(el) {
            if(el.constructor.name.match(/HTML.+?Element/)){
				el.style.display = 'block';
			}else{
				for(let i = 0; i < el.length; i++){
					this._show(el[i]);
				}
			}
        }

        _isHidden(el) {
            return !el.classList.contains(this.settings.classNames.enterTo);
        }

        _getStyle(el, prop) {
            return getComputedStyle(el)[prop];
        }
    }
    //~ window.DLAnimate = new Animator();

