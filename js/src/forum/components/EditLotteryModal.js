import app from 'flarum/forum/app';

import Button from 'flarum/common/components/Button';
import Stream from 'flarum/common/utils/Stream';
import extractText from 'flarum/common/utils/extractText';
import CreateLotteryModal from './CreateLotteryModal';

export default class EditLotteryModal extends CreateLotteryModal {
  oninit(vnode) {
    super.oninit(vnode);

    this.lottery = this.attrs.lottery;

    this.options = this.lottery.options();
    this.optionAnswers = this.options.map((o) => Stream(o.answer()));
    this.optionImageUrls = this.options.map((o) => Stream(o.imageUrl()));
    this.question = Stream(this.lottery.question());
    this.endDate = Stream(this.formatDate(this.lottery.endDate()));
    this.publicLottery = Stream(this.lottery.publicLottery());
    this.allowMultipleVotes = Stream(this.lottery.allowMultipleVotes());
    this.hideVotes = Stream(this.lottery.hideVotes());
    this.allowChangeVote = Stream(this.lottery.allowChangeVote());
    this.maxVotes = Stream(this.lottery.maxVotes() || 0);

    if (this.endDate() && dayjs(this.lottery.endDate()).isAfter(dayjs())) {
      this.datepickerMinDate = this.formatDate(this.endDate());
    }
  }

  title() {
    return app.translator.trans('nodeloc-lottery.forum.modal.edit_title');
  }

  displayOptions() {
    return this.options.map((opt, i) => (
      <div className="Form-group">
        <fieldset className="Lottery-answer-input">
          <input
            className="FormControl"
            type="text"
            name={'answer' + (i + 1)}
            bidi={this.optionAnswers[i]}
            placeholder={app.translator.trans('nodeloc-lottery.forum.modal.option_placeholder') + ' #' + (i + 1)}
          />
          {app.forum.attribute('allowLotteryOptionImage') ? (
            <input
              className="FormControl"
              type="text"
              name={'answerImage' + (i + 1)}
              bidi={this.optionImageUrls[i]}
              placeholder={app.translator.trans('nodeloc-lottery.forum.modal.image_option_placeholder') + ' #' + (i + 1)}
            />
          ) : null}
        </fieldset>

        {i >= 2
          ? Button.component({
              type: 'button',
              className: 'Button LotteryModal--button',
              icon: 'fas fa-minus',
              onclick: i >= 2 ? this.removeOption.bind(this, i) : '',
            })
          : ''}
      </div>
    ));
  }

  addOption() {
    const max = Math.max(app.forum.attribute('lotteryMaxOptions'), 2);

    if (this.options.length < max) {
      this.options.push(app.store.createRecord('lottery_options'));
      this.optionAnswers.push(Stream(''));
      this.optionImageUrls.push(Stream(''));
    } else {
      alert(extractText(app.translator.trans('nodeloc-lottery.forum.modal.max', { max })));
    }
  }

  removeOption(i) {
    this.options.splice(i, 1);
    this.optionAnswers.splice(i, 1);
    this.optionImageUrls.splice(i, 1);
  }

  data() {
    const options = this.options.map((o, i) => {
      if (!o.data.attributes) o.data.attributes = {};

      o.data.attributes.answer = this.optionAnswers[i]();
      o.data.attributes.imageUrl = this.optionImageUrls[i]();

      return o.data;
    });

    return {
      question: this.question(),
      endDate: this.dateToTimestamp(this.endDate()),
      publicLottery: this.publicLottery(),
      hideVotes: this.hideVotes(),
      allowChangeVote: this.allowChangeVote(),
      allowMultipleVotes: this.allowMultipleVotes(),
      maxVotes: this.maxVotes(),
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
