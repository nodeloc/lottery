import app from 'flarum/forum/app';

import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import LogInModal from 'flarum/forum/components/LogInModal';
import ListLotteryModal from './ListLotteryModal';
import classList from 'flarum/common/utils/classList';
import ItemList from 'flarum/common/utils/ItemList';
import Tooltip from 'flarum/common/components/Tooltip';
import icon from 'flarum/common/helpers/icon';
import EditLotteryModal from './EditLotteryModal';
import EventCountDown from "./EventCountDown"; // You need to implement a Countdown component for displaying the countdown

export default class PostLottery extends Component {
  oninit(vnode) {
    super.oninit(vnode);

    this.loadingOptions = false;
    this.useSubmitUI = !this.attrs.lottery?.canCancelEnter() && this.attrs.lottery;
    this.pendingSubmit = false;
  }

  oncreate(vnode) {
    super.oncreate(vnode);
    this.preventClose = this.preventClose.bind(this);
    window.addEventListener('beforeunload', this.preventClose);
  }

  onremove(vnode) {
    super.onremove(vnode);
    window.removeEventListener('beforeunload', this.preventClose);
  }

  view() {
    const lottery = this.attrs.lottery;
    const options = lottery.options() || [];
    const infoItems = this.infoItems();
    const endDate = dayjs(lottery.endDate());
    const hasEntered = lottery.lottery_participants()?.length > 0;
    return (
      <div className="Post-lottery" data-id={lottery.id()}>
        <div className="LotteryHeading">
          <h3 className="LotteryHeading-title">{lottery.prizes()}</h3>
            <Tooltip text={app.translator.trans('nodeloc-lottery.forum.public_lottery')}>
              <Button className="Button LotteryHeading-voters" onclick={this.showparticipants.bind(this)} icon="fas fa-user" />
            </Tooltip>
          {lottery.canEdit() && (
            <Tooltip text={app.translator.trans('nodeloc-lottery.forum.moderation.edit')}>
              <Button className="Button LotteryHeading-edit" onclick={app.modal.show.bind(app.modal, EditLotteryModal, { lottery })} icon="fas fa-pen" />
            </Tooltip>
          )}
          {lottery.canDelete() && (
            <Tooltip text={app.translator.trans('nodeloc-lottery.forum.moderation.delete')}>
              <Button className="Button LotteryHeading-delete" onclick={this.deleteLottery.bind(this)} icon="fas fa-trash" />
            </Tooltip>
          )}
        </div>
        <div>
          <div className="PrizeInfo">
            <div className="PrizeDetails">
              <div>{app.translator.trans('nodeloc-lottery.forum.modal.lottery_placeholder')}: {lottery.prizes()}</div>
              <div>{app.translator.trans('nodeloc-lottery.forum.modal.amount')}: {lottery.amount()}</div>
              <div>{app.translator.trans('nodeloc-lottery.forum.modal.price')}: {lottery.price()}</div>
              <div>{app.translator.trans('nodeloc-lottery.forum.modal.min_participants')}: {lottery.min_participants()}</div>
              <div>{app.translator.trans('nodeloc-lottery.forum.modal.max_participants')}: {lottery.max_participants()}</div>
            </div>
            <EventCountDown endDate={endDate} />
          </div>
          <div className="LotteryOptions">
            <h2 class="event-text"><i class='fas fa-info-circle fontawicon'></i> {app.translator.trans('nodeloc-lottery.forum.modal.options_label')}</h2>
            <ul>
            {options.map(this.viewOption.bind(this))}
            </ul>
          </div>
          <div className="Lottery-sticky">
            {!infoItems.isEmpty() && <div className="helpText LotteryInfoText">{infoItems.toArray()}</div>}
            {this.useSubmitUI && !hasEntered &&(
              <Button className="Button Button--primary Lottery-submit" loading={this.loadingOptions} onclick={this.onsubmit.bind(this)}>
                {app.translator.trans('nodeloc-lottery.forum.lottery.submit_button')}
              </Button>
            )}
          </div>
        </div>
      </div>
    );
  }

  infoItems() {
    const items = new ItemList();
    const lottery = this.attrs.lottery;
    const hasEntered = lottery.lottery_participants()?.length > 0;

    if (app.session.user && !lottery.canEnter() && !lottery.hasEnded()) {
      items.add(
        'no-permission',
        <span>
          <i className="icon fas fa-times-circle fa-fw" />
          {app.translator.trans('nodeloc-lottery.forum.no_permission')}
        </span>
      );
    }

    if (lottery.endDate()) {
      items.add(
        'end-date',
        <span>
          <i class="icon fas fa-clock fa-fw" />
          {lottery.hasEnded()
            ? app.translator.trans('nodeloc-lottery.forum.lottery_ended')
            : app.translator.trans('nodeloc-lottery.forum.days_remaining', { time: dayjs(lottery.endDate()).fromNow() })}
        </span>
      );
    }

    if (hasEntered) {
      items.add(
          'had-enter',
          <span>
          <i class="icon fas fa-check-double fa-fw" />
          {app.translator.trans('nodeloc-lottery.forum.had_enter')}
        </span>
      );
    }
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
  };

  viewOption(opt) {
    const operatorText = this.select_options[opt.operator_type()] || '';
    const operatorSymbol = opt.operator() === 0 ? '<' : '>';
    return (
        <li>{operatorText}{operatorSymbol}{opt.operator_value()}</li>
    );
  }

  onsubmit() {
    if (!app.session.user) {
      app.modal.show(LogInModal);
      return;
    }
    return this.submit( () => {
      this.pendingSubmit = false;
    });
  }

  submit(optionIds, cb, onerror) {
    this.loadingOptions = true;

    m.redraw();
    return app
      .request({
        method: 'PATCH',
        url: `${app.forum.attribute('apiUrl')}/nodeloc/lottery/${this.attrs.lottery.id()}/enter`,
      })
      .then((res) => {
        app.store.pushPayload(res);
        cb?.();
      })
      .catch((err) => {
        onerror?.(err);
      })
      .finally(() => {
        this.loadingOptions = false;

        m.redraw();
      });
  }

  showparticipants() {
    // Load all the votes only when opening the votes list
    app.modal.show(ListLotteryModal, {
      lottery: this.attrs.lottery,
      post: this.attrs.post,
    });
  }

  deleteLottery() {
    if (confirm(app.translator.trans('nodeloc-lottery.forum.moderation.delete_confirm'))) {
      this.attrs.lottery.delete().then(() => {
        m.redraw.sync();
      });
    }
  }

  /**
   * Alert before navigating away using browser's 'beforeunload' event
   */
  preventClose(e) {
      e.preventDefault();
      return true;
  }
}
