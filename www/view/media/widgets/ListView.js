/**
 * Created by Healer on 14-6-5.
 */

$class("ListView", [kx.Weblet, kx.ActionMixin, kx.EventMixin],
{
    _headers: null,

    _templateString: "<table class='table table-striped table-bordered table-hover table-full-width dataTable'><thead></thead><tbody></tbody></table>",

    _data: null,

    _currentPage: 1,

    __constructor: function() {

    },

    onCreated: function(domNode) {

    },

    setPage: function(page) {
        this._currentPage = page;
    },

    getPage: function(page) {
        return this._currentPage;
    },

    setPageEvent: function(event) {
        var this_ = this;
    },

    refresh: function(api, payload, handler) {
        // kx.bind(this, "dataReceived")
        this.ajax(api, payload, handler);
    },

    setHeaders: function(headers) {

        this._headers = headers;
        var thead = this._domNode.find("thead");

        var cl = ["<tr>"];
        for (var i in headers)
        {
            if (headers[i]['type'] == 'id')
            {
                if (headers[i]['checkbox'])
                {
                    cl.push('<td style="width: 20px"><input type="checkbox" class="check-all"/></td>');
                }
                continue;
            }

            cl.push('<td>');
            cl.push(headers[i]['name']);
            cl.push('</td>');
        }
        var html = cl.join("");

        thead.append($(html));

        var this_ = this;
        this._domNode.find('.check-all').change( function () {
            // console.log(this_._domNode.find('.check-all').attr('checked'));
            var c = this_._domNode.find('.check-all').attr('checked');
            this_._domNode.find('.check-one').attr('checked', !!c);
        });
    },

    checkAllItems: function(check) {
        this._domNode.find('.check-all').attr('checked', check);
    },

    getCheckedItems: function () {
        return this._domNode.find('.check-one:checked');
    },

    ///////////////////////////////////////////////////////////
    dataReceived: function(data) {
        console.log('No use now!!!')
        var results = eval("(" + data + ")")['results'];
        var items = results['items']
        this._items = items;
        this.fillItems(this._items);
    },

    clearValues: function() {
        var tbody = this._domNode.find("tbody");
        tbody.empty();


        var headers = [];
        for (var j in this._headers)
        {
            headers.push(this._headers[j]['key']);
        }

        return {
            tbody: tbody,
            headers: headers };
    },

    addColumnData: function (clz, item) {
        
        var tbody = this._domNode.find("tbody");
        tbody.find('tr').each(function (row) {
            // console.log(row)
            $(this).find(clz).html(item);
        })
    },

    addValue: function(item, params) {
        // console.log(item);
        var tbody = params.tbody;
        var headers = params.headers;

        var cl = ["<tr>"];

        var id = null;
        for (var j in headers)
        {
            var key = headers[j];

            if (this._headers[j]['type'] == 'id')
            {
                id = item[key];
                continue;
            }

            if (this._headers[j]['class']) {
                cl.push('<td class="' + this._headers[j]['class'] +'">');
            } else {
                cl.push('<td>');
            }
            var itemType = this._headers[j]['type'];

            if (key == 'handle')
            {
                cl.push(item['handle']);
            }
            else if (key == 'checkbox')
            {
                var cb = '<input type="checkbox" class="check-one" item-id="' + item['id'] + '"/>';
                cl.push(cb);
            }
            else if (itemType == 'url')
            {
                var path = item[key];
                if (path) {
                    var fileName = path.substr(path.lastIndexOf('/') + 1);
                    cl.push("<a href=" + item[key] + ">" + fileName + "</a>");
                } else {
                    cl.push("---");
                }
            }
            else if (itemType == 'link')
            {
                var v = item[key];
                cl.push("<a href=" + item[key] + ">" + v + "</a>");
            }
            else if (itemType == 'function')
            {
                var func = this._headers[j]['function'];
                cl.push(func.apply(null, item[key]));
            }
            else
            {
                if (itemType == 'num')
                {
                    var accuracy = this._headers[j]['accuracy'] || 2;
                    cl.push(parseFloat(item[key]).toFixed(accuracy));
                }
                else
                {
                    cl.push(item[key]);
                }
            }
            cl.push('</td>');

        }

        cl.push("</tr>");

        var html = cl.join("");

        var tr = $(html);
        if (id)
        {
            tr.attr('data-id', id);
        }
        tbody.append(tr);

    },


    addEntry: function(item) {
        var tbody = this._domNode.find("tbody");

        var headers = [];
        for (var j in this._headers)
        {
            headers.push(this._headers[j]['key']);
        }

        var cl = ["<tr>"];

        var id = null;
        for (var j in headers)
        {
            var key = headers[j];

            if (this._headers[j]['type'] == 'id')
            {
                id = item[key];
                continue;
            }
            var css = this._headers[j]['css'];
            if (!css)
            {
                cl.push('<td>');
            }
            else
            {
                cl.push('<td class="' + css + '">');
            }

            if (key == 'handle')
            {
                cl.push(item['handle']);
            }
            else if (this._headers[j]['type'] == 'url')
            {
                cl.push("<a href=" + item[key] + ">链接</a>");
            }
            else if (this._headers[j]['type'] == 'input')
            {
                cl.push("<input value='" + item[key] + "'/>");
            }
            else if (this._headers[j]['type'] == 'button')
            {
                var bindType = this._headers[j]['bind'];
                cl.push("<a class='btn red mini' bind='" + item[bindType]+ "'>" + item[key] + "</a>");
            }
            else if (key == 'checkbox')
            {
                // console.log(item['checkbox'])
                if (item['checkbox'])
                {
                    var cb = '<input type="checkbox" checked="true" class="check-one" item-id="' + item['id'] + '"/>';
                    cl.push(cb);
                }
                else
                {
                    var cb = '<input type="checkbox" class="check-one" item-id="' + item['id'] + '"/>';
                    cl.push(cb);
                }
            }
            else
            {
                cl.push(item[key]);
            }
            cl.push('</td>');

        }

        cl.push("</tr>");

        var html = cl.join("");

        var tr = $(html);
        if (id)
        {
            tr.attr('data-id', id);
        }
        tbody.append(tr);
        return tr;
    },

    fillItems: function(items, pageCount) {
        pageCount = pageCount || 50;
        var tbody = this._domNode.find("tbody");
        tbody.empty();

        var headers = [];
        for (var j in this._headers)
        {
            headers.push(this._headers[j]['key']);
        }

        var index = this._currentPage - 1;
        for (var i in items)
        {
            if (i < index * pageCount)
                continue;

            if (i >= (index + 1) * pageCount)
                break;

            var cl = ["<tr>"];
            var item = items[i];
            var id = null;
            for (var j in headers)
            {
                var key = headers[j];
                cl.push('<td>');
                if (this._headers[j]['type'] == 'id')
                {
                    id = item[key];
                    var cb = '<input type="checkbox" class="check-one" item-id="' + id + '"/>';
                    cl.push(cb);

                    continue;
                }


                if (key == 'handle')
                {
                    cl.push("<a class='btn blue handle'>处理</a>&nbsp;<input class='comment' placeholder='处理意见'/>");
                }
                else if (this._headers[j]['type'] == 'url')
                {
                    cl.push("<a href=" + item[key] + ">链接</a>");
                }
                else
                {
                    if (this._headers[j]['type'] == 'num')
                    {
                        cl.push(item[key].toFixed(1));
                    }
                    else if (this._headers[j]['type'] == 'str')
                    {
                        cl.push(item[key]);
                    }
                    else
                    {
                        cl.push(item[key]);
                    }
                }
                cl.push('</td>');

            }

            cl.push("</tr>");

            var html = cl.join("");

            var tr = $(html);
            if (id)
            {
                tr.attr('data-id', id);
            }
            tbody.append(tr);
        }
    }
});