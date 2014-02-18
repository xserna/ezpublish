/*
YUI 3.14.0 (build a01e97d)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("datatable-keynav",function(e,t){var n=e.Array.each,r=function(){};r.KEY_NAMES={8:"backspace",9:"tab",13:"enter",27:"esc",32:"space",33:"pgup",34:"pgdown",35:"end",36:"home",37:"left",38:"up",39:"right",40:"down",112:"f1",113:"f2",114:"f3",115:"f4",116:"f5",117:"f6",118:"f7",119:"f8",120:"f9",121:"f10",122:"f11",123:"f12"},r.ARIA_ACTIONS={left:"_keyMoveLeft",right:"_keyMoveRight",up:"_keyMoveUp",down:"_keyMoveDown",home:"_keyMoveRowStart",end:"_keyMoveRowEnd",pgup:"_keyMoveColTop",pgdown:"_keyMoveColBottom"},r.ATTRS={focusedCell:{setter:"_focusedCellSetter"},keyIntoHeaders:{value:!0}},e.mix(r.prototype,{keyActions:null,_keyNavSubscr:null,_keyNavTHead:null,_keyNavNestedHeaders:!1,_keyNavColPrefix:null,_keyNavColRegExp:null,initializer:function(){this.onceAfter("render",this._afterKeyNavRender),this._keyNavSubscr=[this.after("focusedCellChange",this._afterKeyNavFocusedCellChange),this.after("focusedChange",this._afterKeyNavFocusedChange)],this._keyNavColPrefix=this.getClassName("col",""),this._keyNavColRegExp=new RegExp(this._keyNavColPrefix+"(.+?)(\\s|$)"),this.keyActions=e.clone(r.ARIA_ACTIONS)},destructor:function(){n(this._keyNavSubscr,function(e){e&&e.detach&&e.detach()})},_afterKeyNavFocusedCellChange:function(e){var t=e.newVal,n=e.prevVal;n&&n.set("tabIndex",-1),t?(t.set("tabIndex",0),this.get("focused")&&(t.scrollIntoView(),t.focus())):this.set("focused",null)},_afterKeyNavFocusedChange:function(e){var t=this.get("focusedCell");e.newVal?t?(t.scrollIntoView(),t.focus()):this._keyMoveFirst():t&&t.blur()},_afterKeyNavRender:function(){var e=this.get("contentBox");this._keyNavSubscr.push(e.on("keydown",this._onKeyNavKeyDown,this),e.on("click",this._onKeyNavClick,this)),this._keyNavTHead=(this._yScrollHeader||this._tableNode).one("thead"),this._keyMoveFirst(),this._keyNavNestedHeaders=this.get("columns").length!==this.head.theadNode.all("th").size()},_onKeyNavClick:function(e){var t=e.target.ancestor(this.get("keyIntoHeaders")?"td, th":"td",!0);t&&(this.focus(),this.set("focusedCell",t))},_onKeyNavKeyDown:function(e){var t=e.keyCode,i=r.KEY_NAMES[t]||t,s;n(["alt","ctrl","meta","shift"],function(n){e[n+"Key"]&&(t=n+"-"+t,i=n+"-"+i)}),s=this.keyActions[t]||this.keyActions[i],typeof s=="string"?this[s]?this[s].call(this,e):this._keyNavFireEvent(s,e):s.call(this,e)},_keyNavFireEvent:function(e,t){var n=t.target.ancestor("td, th",!0);n&&this.fire(e,{cell:n,row:n.ancestor("tr"),record:this.getRecord(n),column:this.getColumn(n.get("cellIndex"))},t)},_keyMoveFirst:function(){this.set("focusedCell",this.get("keyIntoHeaders")?this._keyNavTHead.one("th"):this._tbodyNode.one("td"),{src:"keyNav"})},_keyMoveLeft:function(e){var t=this.get("focusedCell"),n=t.get("cellIndex"),r=t.ancestor();e.preventDefault();if(n===0)return;t=r.get("cells").item(n-1),this.set("focusedCell",t,{src:"keyNav"})},_keyMoveRight:function(e){var t=this.get("focusedCell"),n=t.ancestor("tr"),r=n.ancestor(),i=r===this._keyNavTHead,s,o;e.preventDefault(),s=t.next();if(n.get("rowIndex")!==0&&i&&this._keyNavNestedHeaders)if(s)t=s;else{o=this._getTHParent(t);if(!o||!o.next())return;t=o.next()}else{if(!s)return;t=s}this.set("focusedCell",t,{src:"keyNav"})},_keyMoveUp:function(e){var t=this.get("focusedCell"),n=t.get("cellIndex"),r=t.ancestor("tr"),i=r.get("rowIndex"),s=r.ancestor(),o=s.get("rows"),u=s===this._keyNavTHead,a;e.preventDefault(),u||(i-=s.get("firstChild").get("rowIndex"));if(i===0){if(u||!this.get("keyIntoHeaders"))return;s=this._keyNavTHead,o=s.get("rows"),this._keyNavNestedHeaders?(key=this._getCellColumnName(t),t=s.one("."+this._keyNavColPrefix+key),n=t.get("cellIndex"),r=t.ancestor("tr")):(r=s.get("firstChild"),t=r.get("cells").item(n))}else u&&this._keyNavNestedHeaders?(key=this._getCellColumnName(t),a=this._columnMap[key]._parent,a&&(t=s.one("#"+a.id))):(r=o.item(i-1),t=r.get("cells").item(n));this.set("focusedCell",t)},_keyMoveDown:function(e){var t=this.get("focusedCell"),n=t.get("cellIndex"),r=t.ancestor("tr"),i=r.get("rowIndex")+1,s=r.ancestor(),o=s===this._keyNavTHead,u=this.body&&this.body.tbodyNode,a=s.get("rows"),f,l;e.preventDefault(),o&&(this._keyNavNestedHeaders?(f=this._getCellColumnName(t),l=this._columnMap[f].children,i+=(t.getAttribute("rowspan")||1)-1,l?t=s.one("#"+l[0].id):(t=u.one("."+this._keyNavColPrefix+f),s=u,a=s.get("rows")),n=t.get("cellIndex")):(r=u.one("tr"),t=r.get("cells").item(n))),i-=a.item(0).get("rowIndex");if(i>=a.size()){if(!o)return;s=u,r=s.one("tr")}else r=a.item(i);this.set("focusedCell",r.get("cells").item(n))},_keyMoveRowStart:function(e){var t=this.get("focusedCell").ancestor();this.set("focusedCell",t.get("firstChild"),{src:"keyNav"}),e.preventDefault()},_keyMoveRowEnd:function(e){var t=this.get("focusedCell").ancestor();this.set("focusedCell",t.get("lastChild"),{src:"keyNav"}),e.preventDefault()},_keyMoveColTop:function(e){var t=this.get("focusedCell"),n=t.get("cellIndex"),r,i;e.preventDefault();if(this._keyNavNestedHeaders&&this.get("keyIntoHeaders")){r=this._getCellColumnName(t),i=this._columnMap[r];while(i._parent)i=i._parent;t=this._keyNavTHead.one("#"+i.id)}else t=(this.get("keyIntoHeaders")?this._keyNavTHead:this._tbodyNode).get("firstChild").get("cells").item(n);this.set("focusedCell",t,{src:"keyNav"})},_keyMoveColBottom:function(e){var t=this.get("focusedCell"),n=t.get("cellIndex");this.set("focusedCell",this._tbodyNode.get("lastChild").get("cells").item(n),{src:"keyNav"}),e.preventDefault()},_focusedCellSetter:function(t){if(t instanceof e.Node){var n=t.get("tagName").toUpperCase();if((n==="TD"||n==="TH")&&this.get("contentBox").contains(t))return t}else if(t===null)return t;return e.Attribute.INVALID_VALUE},_getTHParent:function(e){var t=this._getCellColumnName(e),n=this._columnMap[t]&&this._columnMap[t]._parent;return n?e.ancestor().ancestor().one("."+this._keyNavColPrefix+n.key):null},_getCellColumnName:function(e){return e.getData("yui3-col-id")||this._keyNavColRegExp.exec(e.get("className"))[1]}}),e.DataTable.KeyNav=r,e.Base.mix(e.DataTable,[r])},"3.14.0"
,{requires:["datatable-base"]});
