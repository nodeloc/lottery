import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';

export default class DrawLotteryNotification extends Notification {
  icon() {
    return 'fas fa-trophy';
  }

  href() {
    const notification = this.attrs.notification;
    const discussion = notification.subject();

    return app.route.discussion(discussion);
  }

  content() {
    const user = this.attrs.notification.fromUser();
    return app.translator.trans('nodeloc-lottery.forum.notification.drawLottery', {
      user: user,
    });
  }

  excerpt() {
    return null;
  }
}
