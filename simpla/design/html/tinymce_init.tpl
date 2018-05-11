<script language="javascript" type="text/javascript" src="design/js/tinymce/tinymce.min.js"></script>

<script language="javascript">
    {literal}
    tinymce.init({
        mode: "specific_textareas",
        editor_selector: /editor/,
        theme: 'modern',
        plugins: 'code print preview  searchreplace autolink directionality ' +
        'visualblocks visualchars fullscreen image link media template ' +
        'codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor ' +
        ' charcount wordcount imagetools ' +
        'contextmenu colorpicker textpattern help',
        toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | visualchars visualblocks nonbreaking template pagebreak restoredraft",
        toolbar_items_size: 'small',
    });
    {/literal}
</script>