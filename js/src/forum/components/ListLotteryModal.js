import app from 'flarum/forum/app';

import Modal from 'flarum/common/components/Modal';
import avatar from 'flarum/common/helpers/avatar';
import username from 'flarum/common/helpers/username';
import Link from 'flarum/common/components/Link';
import Stream from 'flarum/common/utils/Stream';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';

export default class ListLotteryModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
    this.loading = Stream(true);
    app.store
      .find('nodeloc/lottery', this.attrs.lottery.id(), {
        include: 'participants,participants.user,participants.status',
      })
      .then(() => this.loading(false))
      .finally(() => m.redraw());
  }

  className() {
    return 'Modal--medium ParticipantsModal';
  }

  title() {
    return app.translator.trans('nodeloc-lottery.forum.participants_modal.title' + (this.attrs.lottery.hasEnded() ? '_winners':''));
  }

  content() {
    return <div className="Modal-body">{this.loading() ? <LoadingIndicator /> : this.optionContent()}</div>;
  }

  optionContent() {
    const participants = this.attrs.lottery.participants();
    return (
        <div className="ParticipantsModal-option">
          {participants.length ? (
              <div className="ParticipantsModal-list">{participants.map(this.participantsContent.bind(this))}</div>
          ) : (
              <h4>{app.translator.trans('nodeloc-lottery.forum.modal.no_participants')}</h4>
          )}
        </div>
    );
  }
  participantsContent(participants) {
    const user = participants.user();
    // 只在 hasEnded 为 true 时显示 status=1 的用户
    if (this.attrs.lottery.hasEnded() && participants.status() !== 1) {
      return null;
    }
    const attrs = user && { href: app.route.user(user) };

    return (
      <Link {...attrs}>
        {avatar(user)} {username(user)}
      </Link>
    );
  }
}
