$class('Dialog', null, {

    _domNode: null,

    showDialog: function(e) {
        var _thisDialog = this;
        var callback = function() {
            if (_thisDialog.onOk && _thisDialog.onOk()) {
                this.hide();
            }
        };

        var h = this.template;  // Derived class should provide template as a member
        var href = '#' + h.replace('/', '_');
        var target = $(href && href.replace(/.*(?=#[^\s]+$)/, '')); //strip for ie7
        this._domNode = target;

        var option = $.extend({ remote: !/#/.test(href) && href }, {'onOk': callback});
        e && e.preventDefault();

        target.modal(option);
        return target;
    }
});