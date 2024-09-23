import app from 'flarum/forum/app';
import Component from 'flarum/common/Component';
import Stream from 'flarum/common/utils/Stream';

export default class EventCountDown extends Component {
    oninit(vnode) {
        vnode.state.days = Stream();
        vnode.state.hours = Stream();
        vnode.state.minutes = Stream();
        vnode.state.seconds = Stream();
        this.uniqueId = 'clockdiv-' + vnode.attrs.id; // Use the provided id
    }
    oncreate(vnode) {
        var deadline = vnode.attrs.endDate;


        function time_remaining(endtime) {
            var t = Date.parse(endtime) - Date.parse(new Date());
            var seconds = Math.floor((t / 1000) % 60);
            var minutes = Math.floor((t / 1000 / 60) % 60);
            var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
            var days = Math.floor(t / (1000 * 60 * 60 * 24));
            return {total: t, days: days, hours: hours, minutes: minutes, seconds: seconds};
        }

        function run_clock(id, endtime) {
            var clock = document.getElementById(id);

            // get spans where our clock numbers are held
            var days_span = clock.querySelector('.days');
            var hours_span = clock.querySelector('.hours');
            var minutes_span = clock.querySelector('.minutes');
            var seconds_span = clock.querySelector('.seconds');

            function update_clock(id) {
                var t = time_remaining(deadline);
                days_span.innerHTML = t.days;
                hours_span.innerHTML = ('0' + t.hours).slice(-2);
                minutes_span.innerHTML = ('0' + t.minutes).slice(-2);
                seconds_span.innerHTML = ('0' + t.seconds).slice(-2);
                if (t.total < 0) {
                    clearInterval(timeinterval);
                    if (document.getElementById('titleEvent')) {
                        var elem = document.getElementById('titleEvent');
                        elem.parentNode.removeChild(elem);
                    }
                    var finishText = app.translator.trans('nodeloc-lottery.forum.endDateText');
                    var finishDiv = document.getElementById(id);
                    finishDiv.innerHTML = '<h1 class="letterpress">' + finishText + '</h1>';
                }
            }

            update_clock(id);
            var timeinterval = setInterval(() => update_clock(id), 1000);
        }
        run_clock(this.uniqueId, deadline);

    }
    view(vnode) {
        const wgDays = app.translator.trans('nodeloc-lottery.forum.days');
        const wgHours = app.translator.trans('nodeloc-lottery.forum.hours');
        const wgMinutes = app.translator.trans('nodeloc-lottery.forum.minutes');
        const wgSeconds = app.translator.trans('nodeloc-lottery.forum.seconds');
        const wgEvents = app.forum.attribute('event_title') || app.translator.trans('nodeloc-lottery.forum.hurry_up');
        const fontAwIcon = app.forum.attribute('fontawesome_events_icon') || 'fas fa-gift';

        return (
            <div class="countdown-container">
                <h2 class="event-text" id="titleEvent"><i class={fontAwIcon + " " + 'fontawicon'}></i>{wgEvents}</h2>
                <div class="clockdiv" id={this.uniqueId}>
                    <div class="cntdwn-widget">
                        <span class="days">{vnode.state.days()}</span>
                        <div class="smalltext">{wgDays}</div>
                    </div>
                    <div class="cntdwn-widget">
                        <span class="hours">{vnode.state.hours()}</span>
                        <div class="smalltext">{wgHours}</div>
                    </div>
                    <div class="cntdwn-widget">
                        <span class="minutes">{vnode.state.minutes()}</span>
                        <div class="smalltext">{wgMinutes}</div>
                    </div>
                    <div class="cntdwn-widget">
                        <span class="seconds">{vnode.state.seconds()}</span>
                        <div class="smalltext">{wgSeconds}</div>
                    </div>
                </div>
            </div>
        );
    }
}
