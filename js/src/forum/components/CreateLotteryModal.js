import app from 'flarum/forum/app';

import Button from 'flarum/common/components/Button';
import Modal from 'flarum/common/components/Modal';
import Switch from 'flarum/common/components/Switch';
import ItemList from 'flarum/common/utils/ItemList';
import Stream from 'flarum/common/utils/Stream';
import extractText from 'flarum/common/utils/extractText';
import Select from "flarum/common/components/Select";

export default class CreateLotteryModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);

    this.operator_type = [Stream('discussions_started')];
    this.operator = [Stream('1')];
    this.operator_value = [Stream('1')];

    this.prizes = Stream('');
    this.price = Stream('');
    this.amount = Stream('');
    this.endDate = Stream();

    this.allowCancleEnter = Stream(true);
    this.min_participants = Stream(0);
    this.max_participants = Stream(999999);

    this.datepickerMinDate = this.formatDate(undefined);

    const {lottery} = this.attrs;

    // When re-opening the modal for the same discussion composer where we already set lottery attributes
    if (lottery && Array.isArray(lottery.options)) {
      this.operator_type = [];
      this.operator = [];
      this.operator_value = [];
      lottery.options.forEach((option) => {
        this.operator_type.push(Stream(option.operator_type));
        this.operator.push(Stream(option.operator));
        this.operator_value.push(Stream(option.operator_value));
      });

      this.prizes(lottery.prizes);
      this.price(lottery.price);
      this.amount(lottery.amount);
      this.allowCancleEnter(lottery.allowCancleEnter);
      this.min_participants(lottery.min_participants || 0);
      this.max_participants(lottery.max_participants || 999999);

      this.endDate(this.formatDate(lottery.endDate));

      // Replace minimum of 'today' for lottery end date only if the lottery is not already closed
      if (this.endDate() && dayjs(lottery.endDate).isAfter(dayjs())) {
        this.datepickerMinDate = this.formatDate(lottery.endDate);
      }
    }
  }

  title() {
    return app.translator.trans('nodeloc-lottery.forum.modal.add_title');
  }

  className() {
    return 'LotteryDiscussionModal Modal--medium';
  }

  content() {
    return [
      <div className="Modal-body">
        <div className="LotteryDiscussionModal-form">{this.fields().toArray()}</div>
      </div>,
    ];
  }

  fields() {
    const items = new ItemList();

    items.add(
      'prizes',
      <div className="Form-group">
        <label
          className="label">{app.translator.trans('nodeloc-lottery.forum.modal.lottery_placeholder')}</label>
        <input type="text" name="prizes" className="FormControl" bidi={this.prizes}/>
      </div>,
      100
    );
    items.add(
      'price',
      <div className="Form-group">
        <label
          className="label">{app.translator.trans('nodeloc-lottery.forum.modal.price')}</label>

        <input type="number" min="1" name="price" className="FormControl" bidi={this.price}/>
      </div>,
      100
    );
    items.add(
      'amount',
      <div className="Form-group">
        <label
          className="label">{app.translator.trans('nodeloc-lottery.forum.modal.amount')}</label>

        <input type="number" min="1" name="amount" className="FormControl" bidi={this.amount}/>
      </div>,
      100
    );

    items.add(
      'conditions',
      <div className="LotteryModal--answers Form-group">
        <label className="label LotteryModal--answers-title">
          <span>{app.translator.trans('nodeloc-lottery.forum.modal.options_label')}</span>

          {Button.component({
            className: 'Button LotteryModal--button small',
            icon: 'fas fa-plus',
            onclick: this.addOption.bind(this),
          })}
        </label>

        {this.displayOptions()}
      </div>,
      80
    );

    items.add(
      'date',
      <div className="Form-group">
        <label className="label">{app.translator.trans('nodeloc-lottery.forum.modal.date_placeholder')}</label>

        <div className="LotteryModal--date">
          <input
            className="FormControl"
            type="datetime-local"
            name="date"
            bidi={this.endDate}
            min={this.datepickerMinDate}
            max={this.formatDate('2038')}
          />
          {Button.component({
            className: 'Button LotteryModal--button',
            icon: 'fas fa-times',
            onclick: this.endDate.bind(this, null),
          })}
        </div>

        {this.endDate() && (
          <p className="helpText">
            <i class="icon fas fa-clock"/>
            &nbsp;
            {dayjs(this.endDate()).isBefore(dayjs())
              ? app.translator.trans('nodeloc-lottery.forum.lottery_ended')
              : app.translator.trans('nodeloc-lottery.forum.days_remaining', {time: dayjs(this.endDate()).fromNow()})}
          </p>
        )}
      </div>,
      40
    );

    items.add(
      'min-participants',
      <div className="Form-group MinMaxSelector">
        <label
          className="label">{app.translator.trans('nodeloc-lottery.forum.modal.participants_help')}</label>
        <div class="MinMaxSelector--inputs">
          <input type="number" min="0" name="min_participants" className="FormControl"
                 bidi={this.min_participants}/>
          <button class="Button hasIcon" type="button">
            <i aria-hidden="true" class="icon fas fa-less-than-equal Button-icon"></i><span
            class="Button-label"></span></button>
          <input class="FormControl MinMaxSelector--placeholder" disabled
                 placeholder={app.translator.trans('nodeloc-lottery.forum.modal.participants_label')}/>
          <button class="Button hasIcon" type="button"><i aria-hidden="true"
                                                          class="icon fas fa-less-than-equal Button-icon"></i><span
            class="Button-label"></span></button>
          <input type="number" max="999999" name="max_participants" className="FormControl"
                 bidi={this.max_participants}/>
        </div>
      </div>,
      15
    );

    items.add(
      'submit',
      <div className="Form-group">
        {Button.component(
          {
            type: 'submit',
            className: 'Button Button--primary LotteryModal-SubmitButton',
            loading: this.loading,
          },
          app.translator.trans('nodeloc-lottery.forum.modal.submit')
        )}
      </div>,
      -10
    );

    return items;
  }

  select_options = {
    discussions_started: app.translator.trans('nodeloc-lottery.forum.modal.discussions_started'),
    posts_made: app.translator.trans('nodeloc-lottery.forum.modal.posts_made'),
    //likes_received: app.translator.trans('nodeloc-lottery.forum.modal.likes_received'),
    //best_answers: app.translator.trans('nodeloc-lottery.forum.modal.best_answers'),
    //moderator_strikes: app.translator.trans('nodeloc-lottery.forum.modal.moderator_strikes'),
    money: app.translator.trans('nodeloc-lottery.forum.modal.money'),
    lotteries_made: app.translator.trans('nodeloc-lottery.forum.modal.lotteries_made'),
    read_permission: app.translator.trans('nodeloc-lottery.forum.modal.read_permission'),
  };

  displayOptions() {
    return Object.keys(this.operator_value).map((el, i) => (
      <div className="Form-group MinMaxSelector">
        <fieldset className="MinMaxSelector--inputs">
          <span class="Select">
            {Select.component({
              options: this.select_options,
              value: this.operator_type[i](),
              onchange: (value) => {
                this.operator_type[i](value);
                //m.redraw();  // 手动调用 redraw
              },
            })}
            </span>
          <button class="Button hasIcon" type="button"
                  onclick={() => this.operator[i](this.operator[i]() === 0 ? 1 : 0)}>
            {this.operator[i]() === 0 ? (
              <i aria-hidden="true" class="icon fas fa-less-than-equal Button-icon"></i>
            ) : (
              <i aria-hidden="true" class="icon fas fa-greater-than-equal Button-icon"></i>
            )}
            <span class="Button-label"></span>
          </button>

          <input
            className="FormControl"
            type="number"
            name={'operatorvalue' + (i + 1)}
            bidi={this.operator_value[i]}
            placeholder={app.translator.trans('nodeloc-lottery.forum.modal.option_placeholder') + ' #' + (i + 1)}
          />
        </fieldset>
        {i >= 2
          ? Button.component({
            type: 'button',
            className: 'Button Button--warning LotteryModal--button',
            icon: 'fas fa-minus',
            onclick: i >= 1 ? this.removeOption.bind(this, i) : '',
          })
          : ''}
      </div>
    ));
  }

  addOption() {
    const max = 5;

    if (this.operator_value.length < max) {
      this.operator_type.push(Stream('money'));
      this.operator.push(Stream('1'));
      this.operator_value.push(Stream('1'));
    } else {
      alert(extractText(app.translator.trans('nodeloc-lottery.forum.modal.max', {max})));
    }
  }

  removeOption(option) {
    this.operator_type.splice(option, 1);
    this.operator.splice(option, 1);
    this.operator_value.splice(option, 1);
  }

  data() {
    const lottery = {
      prizes: this.prizes(),
      price: this.price(),
      amount: this.amount(),
      endDate: this.dateToTimestamp(this.endDate()),
      allowCancelEnter: this.allowCancleEnter(),
      min_participants: this.min_participants(),
      max_participants: this.max_participants(),
      options: [],
    };
    this.operator_value.forEach((opvalue, index) => {
      if (opvalue()) {
        lottery.options.push({
          operator_type: this.operator_type[index](),
          operator: this.operator[index](),
          operator_value: opvalue(),
        });
      }
    });

    if (this.prizes() === '') {
      alert(app.translator.trans('nodeloc-lottery.forum.modal.include_prizes'));
      return null;
    }
    if (this.price() === '') {
      alert(app.translator.trans('nodeloc-lottery.forum.modal.include_price'));
      return null;
    }
    if (this.amount() === '') {
      alert(app.translator.trans('nodeloc-lottery.forum.modal.include_amount'));
      return null;
    }

    if (lottery.options.length < 1) {
      alert(app.translator.trans('nodeloc-lottery.forum.modal.min'));

      return null;
    }
    if (!this.endDate() || this.endDate() === '') {
      alert(app.translator.trans('nodeloc-lottery.forum.modal.include_end_date'));
      return null;
    }
    return lottery;
  }

  onsubmit(e) {
    e.preventDefault();

    const data = this.data();

    if (data === null) {
      return;
    }
    const promise = this.attrs.onsubmit(data);

    if (promise instanceof Promise) {
      this.loading = true;

      promise.then(this.hide.bind(this), (err) => {
        console.error(err);
        this.onerror(err);
        this.loaded();
      });
    } else {
      app.modal.close();
    }
  }

  formatDate(date, def = false) {
    const dayjsDate = dayjs(date);

    if (date === false || !dayjsDate.isValid()) return def !== false ? this.formatDate(def) : null;

    return dayjsDate.format('YYYY-MM-DDTHH:mm');
  }

  dateToTimestamp(date) {
    const dayjsDate = dayjs(date);

    if (!date || !dayjsDate.isValid()) return false;

    return dayjsDate.format();
  }
}
