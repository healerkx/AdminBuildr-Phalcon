<script>
    function collect() {
        var form = $('#the-form');
        var post = {};
        form.find('[field]').each(function(){
            var $this = $(this);
            var field = $this.attr('field'), value = $this.val();
            if (!String.isEmpty(field)) {
                post[field] = value;
            }
        });
        return post;
    }

    function save() {
        var post = collect();
        console.log(post);
        $.post('{{ saveAction }}', post, function(data){
            var d = data.toJson();
            if (d.error == 0) {
                // TODO: Save success

            } else {

            }
        });
    }

    $(function(){

        $('#the-form a.save').click(function(){
            save();
        });

    });
</script>