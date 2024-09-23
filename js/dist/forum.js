(()=>{var t={n:o=>{var a=o&&o.__esModule?()=>o.default:()=>o;return t.d(a,{a}),a},d:(o,a)=>{for(var n in a)t.o(a,n)&&!t.o(o,n)&&Object.defineProperty(o,n,{enumerable:!0,get:a[n]})},o:(t,o)=>Object.prototype.hasOwnProperty.call(t,o),r:t=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})}},o={};(()=>{"use strict";t.r(o),t.d(o,{components:()=>at,extend:()=>ht,models:()=>lt});const a=flarum.core.compat["forum/app"];var n=t.n(a);const e=flarum.core.compat["common/extend"],r=flarum.core.compat["common/components/Badge"];var s=t.n(r);const i=flarum.core.compat["forum/components/DiscussionList"];var l=t.n(i);const c=flarum.core.compat["common/models/Discussion"];var u=t.n(c);const p=flarum.core.compat["common/utils/classList"];var d=t.n(p);const f=flarum.core.compat["forum/components/DiscussionComposer"];var h=t.n(f);function y(t,o){return y=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(t,o){return t.__proto__=o,t},y(t,o)}function v(t,o){t.prototype=Object.create(o.prototype),t.prototype.constructor=t,y(t,o)}flarum.core.compat["forum/components/ReplyComposer"];const _=flarum.core.compat["common/components/Button"];var b=t.n(_);const g=flarum.core.compat["common/components/Modal"];var w=t.n(g);flarum.core.compat["common/components/Switch"];const M=flarum.core.compat["common/utils/ItemList"];var N=t.n(M);const x=flarum.core.compat["common/utils/Stream"];var D=t.n(x);const L=flarum.core.compat["common/utils/extractText"];var C=t.n(L);const B=flarum.core.compat["common/components/Select"];var E=t.n(B),S=function(t){function o(){for(var o,a=arguments.length,e=new Array(a),r=0;r<a;r++)e[r]=arguments[r];return(o=t.call.apply(t,[this].concat(e))||this).select_options={discussions_started:n().translator.trans("nodeloc-lottery.forum.modal.discussions_started"),posts_made:n().translator.trans("nodeloc-lottery.forum.modal.posts_made"),money:n().translator.trans("nodeloc-lottery.forum.modal.money"),lotteries_made:n().translator.trans("nodeloc-lottery.forum.modal.lotteries_made"),read_permission:n().translator.trans("nodeloc-lottery.forum.modal.read_permission")},o}v(o,t);var a=o.prototype;return a.oninit=function(o){var a=this;t.prototype.oninit.call(this,o),this.operator_type=[D()("discussions_started")],this.operator=[D()("1")],this.operator_value=[D()("1")],this.prizes=D()(""),this.price=D()(""),this.amount=D()(""),this.endDate=D()(),this.allowCancleEnter=D()(!0),this.min_participants=D()(0),this.max_participants=D()(999999),this.datepickerMinDate=this.formatDate(void 0);var n=this.attrs.lottery;n&&Array.isArray(n.options)&&(this.operator_type=[],this.operator=[],this.operator_value=[],n.options.forEach((function(t){a.operator_type.push(D()(t.operator_type)),a.operator.push(D()(t.operator)),a.operator_value.push(D()(t.operator_value))})),this.prizes(n.prizes),this.price(n.price),this.amount(n.amount),this.allowCancleEnter(n.allowCancleEnter),this.min_participants(n.min_participants||0),this.max_participants(n.max_participants||999999),this.endDate(this.formatDate(n.endDate)),this.endDate()&&dayjs(n.endDate).isAfter(dayjs())&&(this.datepickerMinDate=this.formatDate(n.endDate)))},a.title=function(){return n().translator.trans("nodeloc-lottery.forum.modal.add_title")},a.className=function(){return"LotteryDiscussionModal Modal--medium"},a.content=function(){return[m("div",{className:"Modal-body"},m("div",{className:"LotteryDiscussionModal-form"},this.fields().toArray()))]},a.fields=function(){var t=new(N());return t.add("prizes",m("div",{className:"Form-group"},m("label",{className:"label"},n().translator.trans("nodeloc-lottery.forum.modal.lottery_placeholder")),m("input",{type:"text",name:"prizes",className:"FormControl",bidi:this.prizes})),100),t.add("price",m("div",{className:"Form-group"},m("label",{className:"label"},n().translator.trans("nodeloc-lottery.forum.modal.price")),m("input",{type:"number",min:"1",name:"price",className:"FormControl",bidi:this.price})),100),t.add("amount",m("div",{className:"Form-group"},m("label",{className:"label"},n().translator.trans("nodeloc-lottery.forum.modal.amount")),m("input",{type:"number",min:"1",name:"amount",className:"FormControl",bidi:this.amount})),100),t.add("conditions",m("div",{className:"LotteryModal--answers Form-group"},m("label",{className:"label LotteryModal--answers-title"},m("span",null,n().translator.trans("nodeloc-lottery.forum.modal.options_label")),b().component({className:"Button LotteryModal--button small",icon:"fas fa-plus",onclick:this.addOption.bind(this)})),this.displayOptions()),80),t.add("date",m("div",{className:"Form-group"},m("label",{className:"label"},n().translator.trans("nodeloc-lottery.forum.modal.date_placeholder")),m("div",{className:"LotteryModal--date"},m("input",{className:"FormControl",type:"datetime-local",name:"date",bidi:this.endDate,min:this.datepickerMinDate,max:this.formatDate("2038")}),b().component({className:"Button LotteryModal--button",icon:"fas fa-times",onclick:this.endDate.bind(this,null)})),this.endDate()&&m("p",{className:"helpText"},m("i",{class:"icon fas fa-clock"})," ",dayjs(this.endDate()).isBefore(dayjs())?n().translator.trans("nodeloc-lottery.forum.lottery_ended"):n().translator.trans("nodeloc-lottery.forum.days_remaining",{time:dayjs(this.endDate()).fromNow()}))),40),t.add("min-participants",m("div",{className:"Form-group MinMaxSelector"},m("label",{className:"label"},n().translator.trans("nodeloc-lottery.forum.modal.participants_help")),m("div",{class:"MinMaxSelector--inputs"},m("input",{type:"number",min:"0",name:"min_participants",className:"FormControl",bidi:this.min_participants}),m("button",{class:"Button hasIcon",type:"button"},m("i",{"aria-hidden":"true",class:"icon fas fa-less-than-equal Button-icon"}),m("span",{class:"Button-label"})),m("input",{class:"FormControl MinMaxSelector--placeholder",disabled:!0,placeholder:n().translator.trans("nodeloc-lottery.forum.modal.participants_label")}),m("button",{class:"Button hasIcon",type:"button"},m("i",{"aria-hidden":"true",class:"icon fas fa-less-than-equal Button-icon"}),m("span",{class:"Button-label"})),m("input",{type:"number",max:"999999",name:"max_participants",className:"FormControl",bidi:this.max_participants}))),15),t.add("submit",m("div",{className:"Form-group"},b().component({type:"submit",className:"Button Button--primary LotteryModal-SubmitButton",loading:this.loading},n().translator.trans("nodeloc-lottery.forum.modal.submit"))),-10),t},a.displayOptions=function(){var t=this;return Object.keys(this.operator_value).map((function(o,a){return m("div",{className:"Form-group MinMaxSelector"},m("fieldset",{className:"MinMaxSelector--inputs"},m("span",{class:"Select"},E().component({options:t.select_options,value:t.operator_type[a](),onchange:function(o){t.operator_type[a](o)}})),m("button",{class:"Button hasIcon",type:"button",onclick:function(){return t.operator[a](0===t.operator[a]()?1:0)}},0===t.operator[a]()?m("i",{"aria-hidden":"true",class:"icon fas fa-less-than-equal Button-icon"}):m("i",{"aria-hidden":"true",class:"icon fas fa-greater-than-equal Button-icon"}),m("span",{class:"Button-label"})),m("input",{className:"FormControl",type:"number",name:"operatorvalue"+(a+1),bidi:t.operator_value[a],placeholder:n().translator.trans("nodeloc-lottery.forum.modal.option_placeholder")+" #"+(a+1)})),a>=2?b().component({type:"button",className:"Button Button--warning LotteryModal--button",icon:"fas fa-minus",onclick:a>=1?t.removeOption.bind(t,a):""}):"")}))},a.addOption=function(){this.operator_value.length<5?(this.operator_type.push(D()("money")),this.operator.push(D()("1")),this.operator_value.push(D()("1"))):alert(C()(n().translator.trans("nodeloc-lottery.forum.modal.max",{max:5})))},a.removeOption=function(t){this.operator_type.splice(t,1),this.operator.splice(t,1),this.operator_value.splice(t,1)},a.data=function(){var t=this,o={prizes:this.prizes(),price:this.price(),amount:this.amount(),endDate:this.dateToTimestamp(this.endDate()),allowCancelEnter:this.allowCancleEnter(),min_participants:this.min_participants(),max_participants:this.max_participants(),options:[]};return this.operator_value.forEach((function(a,n){a()&&o.options.push({operator_type:t.operator_type[n](),operator:t.operator[n](),operator_value:a()})})),""===this.prizes()?(alert(n().translator.trans("nodeloc-lottery.forum.modal.include_prizes")),null):""===this.price()?(alert(n().translator.trans("nodeloc-lottery.forum.modal.include_price")),null):""===this.amount()?(alert(n().translator.trans("nodeloc-lottery.forum.modal.include_amount")),null):o.options.length<1?(alert(n().translator.trans("nodeloc-lottery.forum.modal.min")),null):this.endDate()&&""!==this.endDate()?o:(alert(n().translator.trans("nodeloc-lottery.forum.modal.include_end_date")),null)},a.onsubmit=function(t){var o=this;t.preventDefault();var a=this.data();if(null!==a){var e=this.attrs.onsubmit(a);e instanceof Promise?(this.loading=!0,e.then(this.hide.bind(this),(function(t){console.error(t),o.onerror(t),o.loaded()}))):n().modal.close()}},a.formatDate=function(t,o){void 0===o&&(o=!1);var a=dayjs(t);return!1!==t&&a.isValid()?a.format("YYYY-MM-DDTHH:mm"):!1!==o?this.formatDate(o):null},a.dateToTimestamp=function(t){var o=dayjs(t);return!(!t||!o.isValid())&&o.format()},o}(w());const O=flarum.core.compat["forum/components/CommentPost"];var P=t.n(O);const I=flarum.core.compat["common/Component"];var k=t.n(I);const T=flarum.core.compat["forum/components/LogInModal"];var j=t.n(T);const z=flarum.core.compat["common/helpers/avatar"];var F=t.n(z);const q=flarum.core.compat["common/helpers/username"];var H=t.n(q);const A=flarum.core.compat["common/components/Link"];var U=t.n(A);const Y=flarum.core.compat["common/components/LoadingIndicator"];var V=t.n(Y),R=function(t){function o(){return t.apply(this,arguments)||this}v(o,t);var a=o.prototype;return a.oninit=function(o){var a=this;t.prototype.oninit.call(this,o),this.loading=D()(!0),n().store.find("nodeloc/lottery",this.attrs.lottery.data.id,{include:"participants,participants.user,participants.status"}).then((function(){return a.loading(!1)})).finally((function(){return m.redraw()}))},a.className=function(){return"Modal--medium ParticipantsModal"},a.title=function(){return n().translator.trans("nodeloc-lottery.forum.participants_modal.title"+(this.attrs.lottery.hasEnded()?"_winners":""))},a.content=function(){return m("div",{className:"Modal-body"},this.loading()?m(V(),null):this.optionContent())},a.optionContent=function(){var t=this.attrs.lottery.participants();return this.attrs.lottery.hasEnded()?m("div",null,m("h2",null,1==this.attrs.lottery.status()?n().translator.trans("nodeloc-lottery.forum.participants_modal.title_winners"):n().translator.trans("nodeloc-lottery.forum.participants_modal.lottery_canceled")),m("div",{className:"ParticipantsModal-option"},t.length?m("div",{className:"ParticipantsModal-list"},t.map(this.winnerContent.bind(this))):m("h4",null,n().translator.trans("nodeloc-lottery.forum.modal.no_participants"))),m("h2",null,n().translator.trans("nodeloc-lottery.forum.participants_modal.title")),m("div",{className:"ParticipantsModal-option"},t.length?m("div",{className:"ParticipantsModal-list"},t.map(this.participantsContent.bind(this))):m("h4",null,n().translator.trans("nodeloc-lottery.forum.modal.no_participants")))):m("div",null,m("h2",null,n().translator.trans("nodeloc-lottery.forum.participants_modal.title")),m("div",{className:"ParticipantsModal-option"},t.length?m("div",{className:"ParticipantsModal-list"},t.map(this.participantsContent.bind(this))):m("h4",null,n().translator.trans("nodeloc-lottery.forum.modal.no_participants"))))},a.winnerContent=function(t){var o=t.user();if(this.attrs.lottery.hasEnded()&&1!==t.status())return"";var a=o&&{href:n().route.user(o)};return m(U(),a,F()(o)," ",H()(o))},a.participantsContent=function(t){var o=t.user(),a=o&&{href:n().route.user(o)};return m(U(),a,F()(o)," ",H()(o))},o}(w());const G=flarum.core.compat["common/components/Tooltip"];var J=t.n(G);flarum.core.compat["common/helpers/icon"];var K=function(t){function o(){return t.apply(this,arguments)||this}v(o,t);var a=o.prototype;return a.oninit=function(o){t.prototype.oninit.call(this,o),this.lottery=this.attrs.lottery,this.options=this.lottery.options(),this.operator_type=this.options.map((function(t){return D()(t.operator_type())})),this.operator=this.options.map((function(t){return D()(t.operator())})),this.operator_value=this.options.map((function(t){return D()(t.operator_value())})),this.prizes=D()(this.lottery.prizes()),this.price=D()(this.lottery.price()),this.amount=D()(this.lottery.amount()),this.endDate=D()(this.formatDate(this.lottery.endDate())),this.min_participants=D()(this.lottery.min_participants()||0),this.max_participants=D()(this.lottery.max_participants()||999999)},a.title=function(){return n().translator.trans("nodeloc-lottery.forum.modal.edit_title")},a.displayOptions=function(){var t=this;return this.options.map((function(o,a){return m("div",{className:"Form-group MinMaxSelector"},m("fieldset",{className:"MinMaxSelector--inputs"},m("span",{class:"Select"},E().component({options:t.select_options,value:t.operator_type[a](),onchange:function(o){t.operator_type[a](o)}}),m("i",{"aria-hidden":"true",class:"icon fas fa-sort Select-caret"})),m("button",{class:"Button hasIcon",type:"button",onclick:function(){return t.operator[a](0===t.operator[a]()?1:0)}},0===t.operator[a]()?m("i",{"aria-hidden":"true",class:"icon fas fa-less-than-equal Button-icon"}):m("i",{"aria-hidden":"true",class:"icon fas fa-greater-than-equal Button-icon"}),m("span",{class:"Button-label"})),m("input",{className:"FormControl",type:"number",name:"operatorvalue"+(a+1),bidi:t.operator_value[a],placeholder:n().translator.trans("nodeloc-lottery.forum.modal.option_placeholder")+" #"+(a+1)})),a>=2?b().component({type:"button",className:"Button Button--warning LotteryModal--button",icon:"fas fa-minus",onclick:a>=1?t.removeOption.bind(t,a):""}):"")}))},a.data=function(){var t=this,o=this.options.map((function(o,a){return o.data.attributes||(o.data.attributes={}),o.data.attributes.operator_type=t.operator_type[a](),o.data.attributes.operator=t.operator[a](),o.data.attributes.operator_value=t.operator_value[a](),o.data}));return{prizes:this.prizes(),price:this.price(),amount:this.amount(),endDate:this.dateToTimestamp(this.endDate()),min_participants:this.min_participants(),max_participants:this.max_participants(),options:o}},a.onsubmit=function(t){var o=this;if(t.preventDefault(),!this.loading)return this.loading=!0,this.lottery.save(this.data()).then((function(){o.hide(),m.redraw()})).catch((function(t){o.loaded(),o.onerror(t)}))},o}(S),Q=function(t){function o(){return t.apply(this,arguments)||this}v(o,t);var a=o.prototype;return a.oninit=function(t){t.state.days=D()(),t.state.hours=D()(),t.state.minutes=D()(),t.state.seconds=D()(),this.uniqueId="clockdiv-"+t.attrs.id},a.oncreate=function(t){var o=t.attrs.endDate;!function(t,a){var e=document.getElementById(t),r=e.querySelector(".days"),s=e.querySelector(".hours"),i=e.querySelector(".minutes"),l=e.querySelector(".seconds");function c(t){var a=function(t){var o=Date.parse(t)-Date.parse(new Date),a=Math.floor(o/1e3%60),n=Math.floor(o/1e3/60%60),e=Math.floor(o/36e5%24);return{total:o,days:Math.floor(o/864e5),hours:e,minutes:n,seconds:a}}(o);if(r.innerHTML=a.days,s.innerHTML=("0"+a.hours).slice(-2),i.innerHTML=("0"+a.minutes).slice(-2),l.innerHTML=("0"+a.seconds).slice(-2),a.total<0){if(clearInterval(m),document.getElementById("titleEvent")){var e=document.getElementById("titleEvent");e.parentNode.removeChild(e)}var c=n().translator.trans("nodeloc-lottery.forum.endDateText");document.getElementById(t).innerHTML='<h1 class="letterpress">'+c+"</h1>"}}c(t);var m=setInterval((function(){return c(t)}),1e3)}(this.uniqueId)},a.view=function(t){var o=n().translator.trans("nodeloc-lottery.forum.days"),a=n().translator.trans("nodeloc-lottery.forum.hours"),e=n().translator.trans("nodeloc-lottery.forum.minutes"),r=n().translator.trans("nodeloc-lottery.forum.seconds"),s=n().forum.attribute("event_title")||n().translator.trans("nodeloc-lottery.forum.hurry_up"),i=n().forum.attribute("fontawesome_events_icon")||"fas fa-gift";return m("div",{class:"countdown-container"},m("h2",{class:"event-text",id:"titleEvent"},m("i",{class:i+" fontawicon"}),s),m("div",{class:"clockdiv",id:this.uniqueId},m("div",{class:"cntdwn-widget"},m("span",{class:"days"},t.state.days()),m("div",{class:"smalltext"},o)),m("div",{class:"cntdwn-widget"},m("span",{class:"hours"},t.state.hours()),m("div",{class:"smalltext"},a)),m("div",{class:"cntdwn-widget"},m("span",{class:"minutes"},t.state.minutes()),m("div",{class:"smalltext"},e)),m("div",{class:"cntdwn-widget"},m("span",{class:"seconds"},t.state.seconds()),m("div",{class:"smalltext"},r))))},o}(k()),W=function(t){function o(){for(var o,a=arguments.length,e=new Array(a),r=0;r<a;r++)e[r]=arguments[r];return(o=t.call.apply(t,[this].concat(e))||this).select_options={discussions_started:n().translator.trans("nodeloc-lottery.forum.modal.discussions_started"),posts_made:n().translator.trans("nodeloc-lottery.forum.modal.posts_made"),read_permission:n().translator.trans("nodeloc-lottery.forum.modal.read_permission"),money:n().translator.trans("nodeloc-lottery.forum.modal.money"),lotteries_made:n().translator.trans("nodeloc-lottery.forum.modal.lotteries_made")},o}v(o,t);var a=o.prototype;return a.oninit=function(o){var a;t.prototype.oninit.call(this,o),this.loadingOptions=!1,this.useSubmitUI=!(null!=(a=this.attrs.lottery)&&a.canCancelEnter())&&this.attrs.lottery,this.pendingSubmit=!1},a.oncreate=function(o){t.prototype.oncreate.call(this,o)},a.onremove=function(o){t.prototype.onremove.call(this,o)},a.view=function(){var t,o=this.attrs.lottery,a=o.options()||[],e=this.infoItems(),r=dayjs(o.endDate()),s=(null==(t=o.lottery_participants())?void 0:t.length)>0;return m("div",{className:"Post-lottery","data-id":o.data.id},m("div",{className:"LotteryHeading"},m("h3",{className:"LotteryHeading-title"},o.prizes()),m(J(),{text:n().translator.trans("nodeloc-lottery.forum.public_lottery")},m(b(),{className:"Button LotteryHeading-voters",onclick:this.showparticipants.bind(this),icon:"fas fa-user"})),o.canEdit()&&m(J(),{text:n().translator.trans("nodeloc-lottery.forum.moderation.edit")},m(b(),{className:"Button LotteryHeading-edit",onclick:n().modal.show.bind(n().modal,K,{lottery:o}),icon:"fas fa-pen"})),o.canDelete()&&m(J(),{text:n().translator.trans("nodeloc-lottery.forum.moderation.delete")},m(b(),{className:"Button LotteryHeading-delete",onclick:this.deleteLottery.bind(this),icon:"fas fa-trash"}))),m("div",null,m("div",{className:"PrizeInfo"},m("div",{className:"PrizeDetails"},m("span",{class:"amount"},n().translator.trans("nodeloc-lottery.forum.modal.amount")," "),m("span",null,o.amount()),m("span",{class:"price"},n().translator.trans("nodeloc-lottery.forum.modal.price")),m("span",null,o.price()),0!==o.min_participants()&&m("[",null,m("span",{class:"min_participants"},n().translator.trans("nodeloc-lottery.forum.modal.min_participants")),m("span",null,o.min_participants())),o.max_participants()<9999&&m("[",null,m("span",{class:"min_participants"},n().translator.trans("nodeloc-lottery.forum.modal.max_participants")),m("span",null,o.max_participants()))),m(Q,{id:o.data.id,endDate:r})),m("div",{className:"LotteryOptions"},o.min_participants()>0&&o.enterCount()&&m(".arrow",[m(".arrow-status",{style:{width:o.enterCount()>o.min_participants()?"100%":o.enterCount()/o.min_participants()*100+"%"}},[m("span.arrow-pointer","已有"+o.enterCount()+"人 / 最低"+o.min_participants()+"人")])]),m("h2",null,m("i",{class:"fas fa-info-circle fontawicon"})," ",n().translator.trans("nodeloc-lottery.forum.modal.options_label")),m("ul",null,a.map(this.viewOption.bind(this)))),m("div",{className:"Lottery-sticky"},!e.isEmpty()&&m("div",{className:"helpText LotteryInfoText"},e.toArray()),this.useSubmitUI&&!s&&!o.hasEnded()&&m(b(),{className:"Button Button--primary Lottery-submit",loading:this.loadingOptions,onclick:this.onsubmit.bind(this)},n().translator.trans("nodeloc-lottery.forum.lottery.submit_button")))))},a.infoItems=function(){var t,o=new(N()),a=this.attrs.lottery,e=(null==(t=a.lottery_participants())?void 0:t.length)>0;return!n().session.user||a.canEnter()||a.hasEnded()||o.add("no-permission",m("span",null,m("i",{className:"icon fas fa-times-circle fa-fw"}),n().translator.trans("nodeloc-lottery.forum.no_permission"))),a.endDate()&&o.add("end-date",m("span",null,m("i",{class:"icon fas fa-clock fa-fw"}),a.hasEnded()?n().translator.trans("nodeloc-lottery.forum.lottery_ended"):n().translator.trans("nodeloc-lottery.forum.days_remaining",{time:dayjs(a.endDate()).fromNow()}))),e&&o.add("had-enter",m("span",null,m("i",{class:"icon fas fa-check-double fa-fw"}),n().translator.trans("nodeloc-lottery.forum.had_enter"))),o},a.viewOption=function(t){var o=this.select_options[t.operator_type()]||"",a=0===t.operator()?"<":">";return m("li",null,o,a,t.operator_value())},a.onsubmit=function(){var t=this;if(n().session.user)return this.submit((function(){t.pendingSubmit=!1}));n().modal.show(j())},a.submit=function(t,o,a){var e=this;return this.loadingOptions=!0,m.redraw(),n().request({method:"PATCH",url:n().forum.attribute("apiUrl")+"/nodeloc/lottery/"+this.attrs.lottery.data.id+"/enter"}).then((function(t){n().store.pushPayload(t),null==o||o()})).catch((function(t){null==a||a(t)})).finally((function(){e.loadingOptions=!1,m.redraw()}))},a.showparticipants=function(){n().modal.show(R,{lottery:this.attrs.lottery,post:this.attrs.post})},a.deleteLottery=function(){confirm(n().translator.trans("nodeloc-lottery.forum.moderation.delete_confirm"))&&this.attrs.lottery.delete().then((function(){m.redraw.sync()}))},o}(k());flarum.core.compat["forum/components/DiscussionPage"],flarum.core.compat["forum/utils/PostControls"];const X=flarum.core.compat["forum/components/Notification"];var Z=t.n(X),$=function(t){function o(){return t.apply(this,arguments)||this}v(o,t);var a=o.prototype;return a.icon=function(){return"fas fa-exclamation-triangle"},a.href=function(){var t=this.attrs.notification.subject();return n().route.discussion(t)},a.content=function(){return this.attrs.notification.fromUser(),n().translator.trans("nodeloc-lottery.forum.notification.fail")},a.excerpt=function(){return null},o}(Z()),tt=function(t){function o(){return t.apply(this,arguments)||this}v(o,t);var a=o.prototype;return a.icon=function(){return"fas fa-check"},a.href=function(){var t=this.attrs.notification.subject();return n().route.discussion(t)},a.content=function(){return n().translator.trans("nodeloc-lottery.forum.notification.finish")},a.excerpt=function(){return null},o}(Z()),ot=function(t){function o(){return t.apply(this,arguments)||this}v(o,t);var a=o.prototype;return a.icon=function(){return"fas fa-trophy"},a.href=function(){var t=this.attrs.notification.subject();return n().route.discussion(t)},a.content=function(){var t=this.attrs.notification.fromUser();return n().translator.trans("nodeloc-lottery.forum.notification.drawLottery",{user:t})},a.excerpt=function(){return null},o}(Z()),at={CreateLotteryModal:S,PostLottery:W,EditLotteryModal:K,ListVotersModal:R};const nt=flarum.core.compat["common/Model"];var et=t.n(nt),rt=function(t){function o(){for(var o,a=arguments.length,n=new Array(a),e=0;e<a;e++)n[e]=arguments[e];return(o=t.call.apply(t,[this].concat(n))||this).prizes=et().attribute("prizes"),o.hasEnded=et().attribute("hasEnded"),o.endDate=et().attribute("endDate"),o.price=et().attribute("price"),o.amount=et().attribute("amount"),o.min_participants=et().attribute("min_participants"),o.max_participants=et().attribute("max_participants"),o.enterCount=et().attribute("enter_count"),o.status=et().attribute("status"),o.canEnter=et().attribute("canEnter"),o.canEdit=et().attribute("canEdit"),o.canDelete=et().attribute("canDelete"),o.canSeeParticipants=et().attribute("canSeeParticipants"),o.canCancelEnter=et().attribute("can_cancel_enter"),o.options=et().hasMany("options"),o.participants=et().hasMany("participants"),o.lottery_participants=et().hasMany("lottery_participants"),o}return v(o,t),o.prototype.apiEndpoint=function(){return"/nodeloc/lottery"+(this.exists?"/"+this.data.id:"")},o}(et()),st=function(t){function o(){for(var o,a=arguments.length,n=new Array(a),e=0;e<a;e++)n[e]=arguments[e];return(o=t.call.apply(t,[this].concat(n))||this).operator_type=et().attribute("operator_type"),o.operator=et().attribute("operator"),o.operator_value=et().attribute("operator_value"),o.lottery=et().hasOne("lottery"),o}return v(o,t),o.prototype.apiEndpoint=function(){return"/nodeloc/lottery/operator"+(this.exists?"/"+this.data.id:"")},o}(et()),it=function(t){function o(){for(var o,a=arguments.length,n=new Array(a),e=0;e<a;e++)n[e]=arguments[e];return(o=t.call.apply(t,[this].concat(n))||this).lottery=et().hasOne("lottery"),o.user=et().hasOne("user"),o.status=et().attribute("status"),o.lotteryId=et().attribute("lotteryId"),o}return v(o,t),o.prototype.apiEndpoint=function(){return"/nodeloc/lottery/"+this.lotteryId()+"/enter"},o}(et()),lt={Lottery:rt,LotteryOption:st,LotteryParticipants:it};const ct=flarum.core.compat["common/extenders"];var mt=t.n(ct);const ut=flarum.core.compat["common/models/Post"];var pt=t.n(ut);const dt=flarum.core.compat["common/models/Forum"];var ft=t.n(dt);const ht=[(new(mt().Store)).add("lottery",rt).add("lottery_options",st).add("lottery_participants",it),new(mt().Model)(pt()).hasOne("lottery").attribute("canStartLottery"),new(mt().Model)(ft()).attribute("canStartLottery"),new(mt().Model)(u()).attribute("hasLottery").attribute("canStartLottery")];n().initializers.add("nodeloc/lottery",(function(){var t;(0,e.extend)(l().prototype,"requestParams",(function(t){t.include.push("firstPost.lottery")})),(0,e.extend)(u().prototype,"badges",(function(t){this.hasLottery()&&t.add("lottery",s().component({type:"lottery",label:n().translator.trans("nodeloc-lottery.forum.tooltip.badge"),icon:"fas fa-gift"}),5)})),(t=h()).prototype.addLottery=function(){var t=this;n().modal.show(S,{lottery:this.composer.fields.lottery,onsubmit:function(o){return t.composer.fields.lottery=o}})},(0,e.extend)(t.prototype,"headerItems",(function(t){var o,a,e=null==(o=this.composer.body)||null==(o=o.attrs)?void 0:o.discussion;(null!=(a=null==e?void 0:e.canStartLottery())?a:n().forum.canStartLottery())&&t.add("lottery",m("a",{className:"ComposerBody-lottery",onclick:this.addLottery.bind(this)},m("span",{className:d()("LotteryLabel",!this.composer.fields.lottery&&"none")},n().translator.trans("nodeloc-lottery.forum.composer_discussion."+(this.composer.fields.lottery?"edit":"add")+"_lottery"))),1)})),(0,e.extend)(t.prototype,"data",(function(t){this.composer.fields.lottery&&(t.lottery=this.composer.fields.lottery)})),(0,e.extend)(P().prototype,"content",(function(t){var o=this.attrs.post;o.isHidden()&&!this.revealContent||!o.lottery()||o.lottery()&&t.push(m(W,{post:o,lottery:o.lottery()}))})),n().notificationComponents.drawLottery=ot,n().notificationComponents.failLottery=$,n().notificationComponents.finishLottery=tt}))})(),module.exports=o})();
//# sourceMappingURL=forum.js.map