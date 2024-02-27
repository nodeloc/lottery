import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';

export default class FinishLotteryNotification extends Notification {
  icon() {
    return 'fas fa-check';
  }

  href() {
    const notification = this.attrs.notification;
    const discussion = notification.subject();

    return app.route.discussion(discussion);
  }

  content() {
    return app.translator.trans('nodeloc-lottery.forum.notification.finish');
  }

  excerpt() {
    return null;
  }
}
