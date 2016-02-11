////////////////////////////////////////////////////////////////////////////////
// Author: Healer
// Email: healer.kx.yu@gmail.com
// 


var kx = {};
kx.config = $config;

var $strings = {};
var $fragments = $fragments || {};
var $groupName = kx.config['group-name'];

///////////////////////////////////////////////////////////////////////////////
// 
function $class(d, b, p)
{
	if ( typeof(d) === "string" )
	{
		var superClassName = null;
		if (b)
		{
			if (b instanceof Array)
			{
				superClassName = b[0]._className
			}
			else
			{
				superClassName = b._className;
			}
		}
		var dn = d;

		
		var s = dn.split(/\./);
		
		var w = window;
		var sl = s.length;
		for(var i = 0; i < sl - 1; ++i)
		{
			w = w[s[i]] = w[s[i]] || {};
		}
		
		d = w[ s[sl - 1] ] = function(){
			var f = function(this_, arguments_, className)
			{
				var clz = $getClassByName(className);
				var superClassName = clz._superClassName;
				if (superClassName)
				{
					f(this_, arguments_, superClassName);
				}
				if (clz.prototype.__constructor)
				{
					clz.prototype.__constructor.apply(this_, arguments_);   
				}
			};

			return function(){
				this._className = dn;
				f(this, arguments, dn);
			};
		}(dn);

		// This method would be lost.
		//d = w[ s[sl - 1] ] = new Function(code);
		d._className = dn;
		d._superClassName = superClassName;

	} else if ( typeof(d) === 'function' ) {
		// d MUST be class Base;
	}

	if (b)
	{
		if (b instanceof Array)
		{
			$.each(b, function(index){
				$.each(b[index].prototype, function(i, v){
					if (index > 0)
					{
						// TODO:
						d.prototype[i] = v;
					}
					else
					{
						d.prototype[i] = v;
					}
				});
			});
		}
		else
		{
			$.each(b.prototype, function(i, v){
				d.prototype[i] = v;
			});
		}
	}

	$.each(p, function(i, v){
		d.prototype[i] = v;
	});

}

function $getClassByName(n)
{
	var s = n.split(/\./);
	var w = window;
	var sl = s.length;
	for(var i = 0; i < sl - 1; ++i)
	{
		w = w[s[i]] = w[s[i]] || {};
	}
	var c = w[s[sl - 1]];
	return c;
}

kx.loadString = function(fileName, pathName)
{
	if (String.isEmpty(fileName))
	{
		return "";
	}

	var filePath = pathName + fileName;
	if ($fragments)
	{
		// console.debug('HTML compiled-files file loaded.');
		var string = $fragments[filePath.toLowerCase()];
		if (!String.isEmpty(string))
			return string;
	}

	try
	{
		var string = null;
		
		$.ajax({
			url: filePath,
			async: false,
			contentType:"application/x-www-form-urlencoded; charset=utf-8",
			dataType: 'text',
			cache: true,
			type: 'GET',
			success: function (data, textStatus, jqXHR)
			{
				string = data;
			}
		});
		return string;
	}
	catch (e)
	{
		alert("ajax string load failed: " + e.description);
	}
	return "";
}



function $templateString(str, vars)
{
	if (!$templateString.templateVarRegex)
	{
		$templateString.templateVarRegex = /\{%\$[a-z0-9-]+%\}/gi;
	}
	var re = $templateString.templateVarRegex;
	var items = str.match(re);
	for (var index in items)
	{
		var item = items[index];
		var key = item.substr(3, item.length - 5);
		str = str.replace(item, vars[key]);
	}
	return str;
}

function $include(fileName, parent, path)
{
	var html = kx.loadString(fileName, path || kx.config["template.path"]);
	$(html).appendTo(parent);
}

function $require(fileName, common)
{
	var widgetPath = common ? kx.config["widget.path"] : kx.config["widget.path"];
	var jsContent = kx.loadString(fileName, widgetPath);
	
	var head = document.getElementsByTagName('HEAD').item(0);
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.text = jsContent;
	head.appendChild(script);
}

function $serviceInvoke(service, data, async, retType, handler)
{
	var retValue = null;
	var httpMethod = data ? "POST" : "GET";
	retType = retType || "text";
	handler = handler || function(retData, textStatus, jqXHR) { retValue = retData; };
	try
	{
		var url = kx.config["action.path"] + service;
		$.ajax({
			url: url,
			async: async,
			data: data,
			dataType: retType,
			cache: false,
			type: httpMethod,
			success: handler
		});
		return retValue;
	}
	catch (e)
	{
		alert("Service invoke failed: " + e.description);
	}
}


kx.bind = function(receiver, func)
{
	return function(){
		if ( typeof(func) === 'string' )
			return receiver[func].apply(receiver, arguments);
		else
			return func.apply(receiver, arguments);
	};
}

function $g11n(key, lang)
{
	lang = lang || 'zh-cn';
	return $strings[lang][key];
}

///////////////////////////////////////////////////////////////
// For JavaScript has a class named 'Object', 
// so use 'Base' instead of the Inherit-Root.
function Base(){
	Base._className = "Base";
	Base._superClassName = null;
}

$class(Base, null, {

	_className: null,

	__constructor: function()
	{
		//console.debug('Base ctor');
	},

	__super: function(func, args)
	{
		var r = func.apply(this, args);
		return r;
	},


	className: function()
	{
		return this._className;
	},

	toString: function()
	{
		return Base._className;
	}
});
// Init Classes system.,
Base();

$class('kx.EventMixin', null, {
	
	bindEvent: function(obj, event, handler)
	{
		if (typeof(handler) === "string")
		{
			handler = kx.bind(this, handler);
		}
		obj._domNode.bind(event, handler);
	},

    unbindEvent: function(obj, event) {
        obj._domNode.unbind(event);
    },

	fireEvent: function(event, args) {
		this._domNode.trigger(event, [this, args]);
        return true;
	},

	bindTextChangedEvent: function(domNode, handler)
	{
		var event = $.browser.msie ? "propertychange" : "input";
		this.bindEvent(domNode, event, handler);
	} 
});

kx.makeRandId = function(n)
{
	n = n || 4;
	var rdm = "";
	for(var i = 0; i < n; i++)
		rdm += Math.floor( Math.random() * 10 );
	return rdm;
}


///////////////////////////////////////////////////////////////
$class("kx.Widget", Base, {

	_widgetId: null,

	_domNode: null,

	__constructor: function(widgetId)
	{
		if (widgetId)
		{
			this._widgetId = widgetId;
		}
		else
		{
			var cn = this._className.toLowerCase();
			this._widgetId = cn + "-" + kx.makeRandId();
		}
	},

    widgetId: function()
    {
        return this._widgetId;
    },

	attach: function(domNode)
	{
		this._domNode = domNode;
		// kx.activeWeb(domNode);
		this.onAttach && this.onAttach(domNode);
	},

	find: function(selector) {
		return this._domNode.find(selector);
	},

	hide: function(hidden)
	{
		var dv = hidden;
		if (hidden == false || hidden == 'false')
		{
			dv = '';
		}
		else if (!hidden || hidden == true || hidden == 'true')
		{
			dv = 'none';
		}
		this._domNode.css('display', dv)
	},
});

///////////////////////////////////////////////////////////////
$class("kx.Weblet", kx.Widget, {

	_templateString: null,

	_templateFile: null,

	_templateVars: null,

	_templateCached: false,

	_common: false,

	__constructor: function(widgetId, templateFile)
	{
		this.__isWeblet = true;
		if (templateFile)
		{
			this._templateFile = templateFile;
			this._templateString = null;
		}
	},

	// return domNode, derived class append it to the parent.
	create: function(params)
	{
		if (!this._templateString)
		{
			// var widgetPath = this._common ? HJ.COMMON_WIDGETS_PATH : HJ.WIDGETS_PATH;
			var templatePath = kx.config["template.path"]

			this._templateString = kx.loadString(this._templateFile, templatePath);
		}

		if (this._templateVars)
		{
			this._templateString = $templateString(this._templateString, this._templateVars);
		}

		this._domNode = this._domNode || $(this._templateString);
		this.onCreated && this.onCreated(this._domNode, params);
		return this._domNode;
	},

	setCommon: function()
	{
		this._common = true;
	},

	findNodes: function(sel)
	{
		return this._domNode.find(sel);
	},

	destroy: function()
	{
		this._domNode.remove();
	},

	toString: function()
	{
		return "Widget";
	}


});

// Type Alias;

Widget = kx.Weblet;



// Static Methods
Widget.load = function( widgetName, common )
{
	var widgetClass = $getClassByName(widgetName);
	if ( !widgetClass )
	{
		var path = widgetName.replace(/\./g, "/") + ".js";
		$require(path, common);
		widgetClass = $getClassByName(widgetName);
	}
	return widgetClass
}

Widget.addWidget = function( widget )
{
	if ( !Widget.widgetsArray )
	{
		Widget.widgetsArray = [];
	}
	var widgetId = widget._widgetId;
	if ( !Widget.widgetById(widgetId) )
	{
		Widget.widgetsArray[widgetId] = widget;
		return true;
	}
	// console.log('Duplicated Widget ID');
	return false;
};

Widget.widgetById = function(widgetId)
{
	if ( !Widget.widgetsArray )
	{
		return null;
	}
	return Widget.widgetsArray[widgetId];
};

Widget.isWidget = function(widget)
{
	if ( typeof(widget) == 'object' )
	{
		if ( !widget._widgetId )
		{
			return false;
		}

		return true;
	}
	return false;
};

// Mixin.
$class('kx.ActionMixin', null, {

	_actionBase: null,

	ajax: function(action, data, handler)
	{
		if (this._actionBase)
			action = this._actionBase + action;
		return $serviceInvoke(action, data, true, 'text', kx.bind(this, function(){
			if ( typeof(handler) == 'string' )
			{
				this[handler].apply(this, arguments);
			}
			else if ( typeof(handler) == 'function' )
			{
				handler.apply(this, arguments);
			}
		}));
	},

});

function $compare(a, b, d)
{
	if (a == b)
	{
		return 0;
	}
	else if ( a < b )
	{
		return d;
	}
	else
	{
		return -d;
	}
}


function onBodyResize()
{
	$('body').trigger('onsize');
}

function activeWeb(args)
{
	var classesBeginTime = new Date().getTime();
	kx.activeWeb($('body'), args);
	var classesEndTime = new Date().getTime();
	console.log( "Main function takes " + (classesEndTime - classesBeginTime) + " ms" );
}

kx.activeWeb = function(node, args)
{
	if (kx.config['handle.includes'])
	{
		var includes = node.attr('includes');
		if ('none' !== includes)
		{
			node.find('div[include]').each(function(){
				var div = $(this);
				var file = div.attr('include');

				if (!String.isEmpty(file))
				{
					$include(file, div);
				}
			});
		}
	}

	// Widget contains kx.Widget and kx.Weblet;
	// <div widget-class='' widget-id='' async='false|true'/>
	node.find('div[widget-class]').each(function(){
		var div = $(this);
        var created = div.attr('widget-created');

        if (created != "true")
        {
            var widgetClassName = div.attr('widget-class');
            var common = div.attr('common');
            if (!String.isEmpty(widgetClassName))
            {
				var widgetClass = $getClassByName(widgetClassName);
				if (!widgetClass)
                    return true;
                var widgetId = div.attr('widget-id');

                var widget = new widgetClass(widgetId);

                if (common)
                {
                    widget.setCommon();
                }
                if (widget.__isWeblet)
                {
                    widget.create(div);
                }
                else
                {
                    widget.attach(div);
                }
                Widget.addWidget(widget);
                div.attr('widget-created', 'true');
            }
        }
	});
}


////////////////////////////////////////////////////////////////////////////////

String.prototype.endsWith = function(p) {
	return this.indexOf(p) + p.length == this.length;
};

String.isEmpty = function(s) {
	return s == undefined || s == null || s.length == 0;
};

String.prototype.toJson = function(s) {
	return eval("(" + this + ")");
};

String.prototype.format = function(args) {
	var result = this;
	if (arguments.length > 0) {
		if (arguments.length == 1 && typeof (args) == "object") {
			for (var key in args) {
				if(args[key]!=undefined){
					var reg = new RegExp("({" + key + "})", "g");
					result = result.replace(reg, args[key]);
				}
			}
		}
		else {
			for (var i = 0; i < arguments.length; i++) {
				if (arguments[i] != undefined) {
					var reg = new RegExp("({[" + i + "]})", "g");
					result = result.replace(reg, arguments[i]);
				}
			}
		}
	}
	return result;
}