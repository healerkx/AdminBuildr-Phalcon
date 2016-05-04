$class('TableFieldsRelation', null, {
    addRelation: function(tableNode, selectNode) {
        tableNode.change(function() {
            var tableName = $(this).val();
            $.post("/ajax/info",    // TODO: Move ajax module?
                { table:tableName },
                function(data){
                    var data = data.toJson();
                    if (data.error == 0) {
                        fillTableFields(selectNode, data.data.fields);
                    }
                });
        });
    }
});
