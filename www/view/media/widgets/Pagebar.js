
$class("Pagebar", [kx.Weblet, kx.EventMixin], 
{
	_templateFile: "pagebar.html",

	_currentPage: 1,

	__constructor: function(p) {

        this._pageCount = p.pageCount;
        this._page = p.page;
	},

	onCreated: function(domNode) {
        var this_ = this;
        var t = domNode.find('li.template');
        var f = domNode.find('li.template');
        var pt = '<li><a style="padding: 0px"><input type="text" placeholder="输入页码" style="width: 60px; margin-bottom: 0px"></a></li>'
        t.removeClass('template');
        var hasPageTextBox = false;
        var left = 8;


        for (var i = 2; i <= this._pageCount; i += 1) {
            var n = t.clone();
            n.attr('data-lp', i).find('a').text(i);

            // ZM：页面太多的时候，中间的部分就显示为input box
            if (this._pageCount > 20 && i > left &&  i < this._pageCount - left + 1)
            {
                if (!hasPageTextBox)
                {
                    var text = $(pt);
                    text.attr('data-lp', 'text').delegate('input', 'keypress', function(e)
                    {
                        if (e.keyCode  == 13)
                        {
                            var page = $(this).val();
                            if (page < 0 || page > this_._pageCount || isNaN(page))
                            {
                                alert('请输入正确的页码')
                                return false;
                            }
                            this_.pageChanged(page);
                        }
                    });
                    hasPageTextBox = true;
                    f.after(text);
                    f = text;
                }
            }
            else
            {
                f.after(n);
                f = n;
            }
        }

        this.boldPage(domNode.find('li[data-lp='+ this._page +']'));
		domNode.find("li").bind("click", kx.bind(this, "pageClicked"));
	},

    boldPage: function(p) {
        // console.log(p)
        p.find('a').css('font-weight', 'bold').css('text-decoration', 'underline');
    },

	pageClicked: function(e) {
        var p = $(e.delegateTarget);

		var page = p.attr('data-lp');
        if (page == 'text')
        {
            return false;
        }

        this._currentPage = page;
        console.log(this._currentPage);
        this._obj.fireEvent(this._event, this._currentPage);
        return false;
    },

    pageChanged: function(p) {
        var page = p;
        this._currentPage = page;
        this._obj.fireEvent(this._event, this._currentPage);

    },

	currentPage: function() {
		return this._currentPage;
	},

    setPageEvent: function(obj, event) {
        this._event = event;
        this._obj = obj;
    }

});