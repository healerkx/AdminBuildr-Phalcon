/**
 * Created by zhuomuniao1 on 14-6-5.
 */
// ZM: classes支持多继承, AdminManagerPane is-a Widget, 同时也支持kx.ActionMixin和kx.EventMixin的功能
// 其中，ActionMixin是表示这个类支持Ajax, EventMixin 表示支持事件通知， 那么派生类从基类集成了这些功能了。

$class("AdminManagerPane", [kx.Widget, kx.ActionMixin, kx.EventMixin],
{
    _userListView: null,

    __constructor: function() {

    },

    onAttach: function(domNode) {

        domNode.find("a.add-admin").click(kx.bind(this, "addAdmin"));
        // ZM：注意一个List对象的创建过程
        // 1， Create先，再把它的domNode append 到已有的HTML结点上。
        // 2. setHeaders, (这样是为后面的数据指明了哪些字段要显示)
        this._userListView = new ListView();
        var userListViewDomNode = this._userListView.create();
        userListViewDomNode.appendTo(domNode.find('div.users'));

        this._userListView.setHeaders([
            {'key':'id', 'type': 'id'},
            {'key':'username', 'name':'用户名'},
            {'key':'handle', 'name':'删除'},

        ]);

        var this_ = this;
        var api = "user/fetch/";
        this.ajax(api, null, function(data){
            var d = eval("(" + data + ")");
            if (d.errorCode == 0) {
                var users = d.results;
                for (var i in users) {
                    var user = users[i];

                    handle = '总管理员';
                    if (user.username != 'admin')
                        handle = "<a class='btn red del'>删除</a>";

                    this_._userListView.addEntry({
                        id: user.user_id,
                        username: user.username,
                        handle: handle

                    });
                }
            }
        });

        this_._userListView._domNode.delegate('td a.del', 'click', function(){
            var tr = $(this).parent().parent();
            var userId = tr.attr('data-id');
            this_.deleteUser(userId, tr);
        });

    },

    deleteUser: function(userId, tr) {
        this.ajax('user/del/' + userId, null, function(data){
                var d = eval("(" + data + ")");
                if (d.errorCode == 0) {
                    tr.find('td').css('background-color', 'yellow');
                    setTimeout(function(){
                        tr.slideUp();
                    }, 500);
                }
        });
    },

    addAdmin: function() {
        var payload = {
            "username": this.getUsername(),
            "password_md5": this.passwordMD5()
        };
        var this_ = this;
        this.ajax("user/register", payload, function(data){
            var d = eval("(" + data + ")");
            if (d.errorCode == 0) {
                var user = d.results;
                this_._userListView.addEntry({
                    id: user.user_id,
                    username: user.username,
                    handle: "<a class='btn red del'>删除</a>"

                });
            }
        });
    },

    passwordMD5: function() {
        var p = this._domNode.find("input.password").val();
        return hex_md5(p);
    },

    getUsername: function() {
        return this._domNode.find("input.username").val();
    }


});