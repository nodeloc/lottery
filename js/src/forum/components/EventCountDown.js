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
        var hadEnded = vnode.attrs.hasEnded;

        function time_remaining(endtime) {
            if (!endtime) return { total: 0, days: 0, hours: 0, minutes: 0, seconds: 0 };
            var t = Date.parse(endtime) - Date.parse(new Date());
            return {
                total: t,
                days: Math.floor(t / (1000 * 60 * 60 * 24)),
                hours: Math.floor((t / (1000 * 60 * 60)) % 24),
                minutes: Math.floor((t / (1000 * 60)) % 60),
                seconds: Math.floor((t / 1000) % 60),
            };
        }

        function update_clock(id, hadEnded, deadline) {
            var clock = document.getElementById(id);
            if (!clock) return;
            var days_span = clock.querySelector('.days');
            var hours_span = clock.querySelector('.hours');
            var minutes_span = clock.querySelector('.minutes');
            var seconds_span = clock.querySelector('.seconds');

            var t = hadEnded ? { total: -1, days: 0, hours: 0, minutes: 0, seconds: 0 } : time_remaining(deadline);
            days_span.innerHTML = t.days;
            hours_span.innerHTML = ('0' + t.hours).slice(-2);
            minutes_span.innerHTML = ('0' + t.minutes).slice(-2);
            seconds_span.innerHTML = ('0' + t.seconds).slice(-2);

            if (t.total < 0) {
                clearInterval(clock.timeinterval);
                var finishText = app.translator.trans('nodeloc-lottery.forum.endDateText');
                clock.innerHTML = `<h1 class="letterpress">${finishText}</h1>`;
            }
        }

        function run_clock(id, deadline, hadEnded) {
            function tick() {
                update_clock(id, hadEnded, deadline);
            }
            tick();
            var clock = document.getElementById(id);
            clock.timeinterval = setInterval(tick, 1000);
        }

        // 调用
        run_clock(this.uniqueId, deadline, hadEnded);


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
