import app from 'flarum/forum/app';

import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import LogInModal from 'flarum/forum/components/LogInModal';
import ListVotersModal from './ListVotersModal';
import classList from 'flarum/common/utils/classList';
import ItemList from 'flarum/common/utils/ItemList';
import Tooltip from 'flarum/common/components/Tooltip';
import icon from 'flarum/common/helpers/icon';
import EditLotteryModal from './EditLotteryModal';

export default class PostLottery extends Component {
  oninit(vnode) {
    super.oninit(vnode);

    this.loadingOptions = false;

    this.useSubmitUI = !this.attrs.lottery?.canChangeVote() && this.attrs.lottery?.allowMultipleVotes();
    this.pendingSubmit = false;
    this.pendingOptions = null;
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
    let maxVotes = lottery.allowMultipleVotes() ? lottery.maxVotes() : 1;

    if (maxVotes === 0) maxVotes = options.length;

    const infoItems = this.infoItems(maxVotes);

    return (
      <div className="Post-lottery" data-id={lottery.id()}>
        <div className="LotteryHeading">
          <h3 className="LotteryHeading-title">{lottery.question()}</h3>

          {lottery.canSeeVoters() && (
            <Tooltip text={app.translator.trans('nodeloc-lottery.forum.public_lottery')}>
              <Button className="Button LotteryHeading-voters" onclick={this.showVoters.bind(this)} icon="fas fa-lottery" />
            </Tooltip>
          )}

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
          <div className="LotteryOptions">{options.map(this.viewOption.bind(this))}</div>

          <div className="Lottery-sticky">
            {!infoItems.isEmpty() && <div className="helpText LotteryInfoText">{infoItems.toArray()}</div>}

            {this.useSubmitUI && this.pendingSubmit && (
              <Button className="Button Button--primary Lottery-submit" loading={this.loadingOptions} onclick={this.onsubmit.bind(this)}>
                {app.translator.trans('nodeloc-lottery.forum.lottery.submit_button')}
              </Button>
            )}
          </div>
        </div>
      </div>
    );
  }

  infoItems(maxVotes) {
    const items = new ItemList();
    const lottery = this.attrs.lottery;
    const hasVoted = lottery.myVotes()?.length > 0;

    if (app.session.user && !lottery.canVote() && !lottery.hasEnded()) {
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

    if (lottery.canVote()) {
      items.add(
        'max-votes',
        <span>
          <i className="icon fas fa-lottery fa-fw" />
          {app.translator.trans('nodeloc-lottery.forum.max_votes_allowed', { max: maxVotes })}
        </span>
      );

      if (!lottery.canChangeVote()) {
        items.add(
          'cannot-change-vote',
          <span>
            <i className={`icon fas fa-${hasVoted ? 'times' : 'exclamation'}-circle fa-fw`} />
            {app.translator.trans('nodeloc-lottery.forum.lottery.cannot_change_vote')}
          </span>
        );
      }
    }

    return items;
  }

  viewOption(opt) {
    const lottery = this.attrs.lottery;
    const hasVoted = lottery.myVotes()?.length > 0;
    const totalVotes = lottery.voteCount();

    const voted = this.pendingOptions ? this.pendingOptions.has(opt.id()) : lottery.myVotes()?.some?.((vote) => vote.option() === opt);
    const votes = opt.voteCount();
    const percent = totalVotes > 0 ? Math.round((votes / totalVotes) * 100) : 0;

    // isNaN(null) is false, so we have to check type directly now that API always returns the field
    const canSeeVoteCount = typeof votes === 'number';
    const isDisabled = this.loadingOptions || (hasVoted && !lottery.canChangeVote());
    const width = canSeeVoteCount ? percent : (Number(voted) / (lottery.myVotes()?.length || 1)) * 100;

    const showCheckmark = !app.session.user || (!lottery.hasEnded() && lottery.canVote() && (!hasVoted || lottery.canChangeVote()));

    const bar = (
      <div className="LotteryBar" data-selected={!!voted} style={`--lottery-option-width: ${width}%`}>
        {showCheckmark && (
          <label className="LotteryAnswer-checkbox checkbox">
            <input onchange={this.changeVote.bind(this, opt)} type="checkbox" checked={voted} disabled={isDisabled} />
            <span className="checkmark" />
          </label>
        )}

        <div className="LotteryAnswer-text">
          <span className="LotteryAnswer-text-answer">{opt.answer()}</span>
          {voted && !showCheckmark && icon('fas fa-check-circle', { className: 'LotteryAnswer-check' })}
          {canSeeVoteCount && <span className={classList('LotteryPercent', percent !== 100 && 'LotteryPercent--option')}>{percent}%</span>}
        </div>

        {opt.imageUrl() ? <img className="LotteryAnswer-image" src={opt.imageUrl()} alt={opt.answer()} /> : null}
      </div>
    );

    return (
      <div
        className={classList('LotteryOption', hasVoted && 'LotteryVoted', lottery.hasEnded() && 'LotteryEnded', opt.imageUrl() && 'LotteryOption-hasImage')}
        data-id={opt.id()}
      >
        {canSeeVoteCount ? (
          <Tooltip text={app.translator.trans('nodeloc-lottery.forum.tooltip.votes', { count: votes })} onremove={this.hideOptionTooltip}>
            {bar}
          </Tooltip>
        ) : (
          bar
        )}
      </div>
    );
  }

  changeVote(option, evt) {
    if (!app.session.user) {
      app.modal.show(LogInModal);
      evt.target.checked = false;
      return;
    }

    const optionIds = this.pendingOptions || new Set(this.attrs.lottery.myVotes().map?.((v) => v.option().id()));
    const isUnvoting = optionIds.delete(option.id());
    const allowsMultiple = this.attrs.lottery.allowMultipleVotes();

    if (!allowsMultiple) {
      optionIds.clear();
    }

    if (!isUnvoting) {
      optionIds.add(option.id());
    }

    if (this.useSubmitUI) {
      this.pendingOptions = optionIds.size ? optionIds : null;
      this.pendingSubmit = !!this.pendingOptions;
      return;
    }

    return this.submit(optionIds, null, () => (evt.target.checked = isUnvoting));
  }

  onsubmit() {
    return this.submit(this.pendingOptions, () => {
      this.pendingOptions = null;
      this.pendingSubmit = false;
    });
  }

  submit(optionIds, cb, onerror) {
    this.loadingOptions = true;
    m.redraw();

    return app
      .request({
        method: 'PATCH',
        url: `${app.forum.attribute('apiUrl')}/nodeloc/lottery/${this.attrs.lottery.id()}/votes`,
        body: {
          data: {
            optionIds: Array.from(optionIds),
          },
        },
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

  showVoters() {
    // Load all the votes only when opening the votes list
    app.modal.show(ListVotersModal, {
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
   * Attempting to use the `tooltipVisible` attr on the Tooltip component set to 'false' when no vote count
   * caused the tooltip to break on click. This is a workaround to hide the tooltip when no vote count is available,
   * called on 'onremove' of the Tooltip component. It doesn't always work as intended either, but it does the job.
   */
  hideOptionTooltip(vnode) {
    vnode.attrs.tooltipVisible = false;
    vnode.state.updateVisibility();
  }

  /**
   * Alert before navigating away using browser's 'beforeunload' event
   */
  preventClose(e) {
    if (this.pendingOptions) {
      e.preventDefault();
      return true;
    }
  }
}
