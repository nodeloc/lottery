import app from 'flarum/forum/app';

import Button from 'flarum/common/components/Button';
import Stream from 'flarum/common/utils/Stream';
import extractText from 'flarum/common/utils/extractText';
import CreateLotteryModal from './CreateLotteryModal';
import Select from "flarum/common/components/Select";

export default class EditLotteryModal extends CreateLotteryModal {
  oninit(vnode) {
    super.oninit(vnode);

    this.lottery = this.attrs.lottery;

    this.options = this.lottery.options();
    this.operator_type = this.options.map((o) => Stream(o.operator_type()));
    this.operator = this.options.map((o) => Stream(o.operator()));
    this.operator_value = this.options.map((o) => Stream(o.operator_value()));
    this.prizes = Stream(this.lottery.prizes());
    this.price = Stream(this.lottery.price());
    this.amount = Stream(this.lottery.amount());
    this.endDate = Stream(this.formatDate(this.lottery.endDate()));
    this.min_participants = Stream(this.lottery.min_participants() || 0);
    this.max_participants = Stream(this.lottery.max_participants() || 999999);

    if (this.endDate() && dayjs(this.lottery.endDate()).isAfter(dayjs())) {
      this.datepickerMinDate = this.formatDate(this.endDate());
    }
  }

  title() {
    return app.translator.trans('nodeloc-lottery.forum.modal.edit_title');
  }

  displayOptions() {
    console.log('options',this.options);
    return this.options.map((options, i) => (
        <div className="Form-group MinMaxSelector">
          <fieldset className="MinMaxSelector--inputs">
          <span class="Select">
            {Select.component({
              options: this.select_options,
              value: this.operator_type[i](),
              onchange: (value) => {
                this.operator_type[i](value);
              },
            })}
            <i aria-hidden="true" class="icon fas fa-sort Select-caret"></i></span>
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
      this.operator_type.push(Stream(''));
      this.operator.push(Stream(''));
      this.operator_value.push(Stream(''));
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
    const options = this.options.map((o, i) => {
      if (!o.data.attributes) o.data.attributes = {};

      o.data.attributes.operator_type = this.operator_type[i]();
      o.data.attributes.operator = this.operator[i]();
      o.data.attributes.operator_value = this.operator_value[i]();

      return o.data;
    });

    return {
      prizes: this.prizes(),
      price: this.price(),
      amount: this.amount(),
      endDate: this.dateToTimestamp(this.endDate()),
      min_participants: this.min_participants(),
      max_participants: this.max_participants(),
      options,
    };
  }

  onsubmit(e) {
    e.preventDefault();

    if (this.loading) return;

    this.loading = true;

    return this.lottery
      .save(this.data())
      .then(() => {
        this.hide();
        m.redraw();
      })
      .catch((e) => {
        this.loaded();
        this.onerror(e);
      });
  }
}
