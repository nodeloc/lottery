import app from 'flarum/forum/app';

import addDiscussionBadge from './addDiscussionBadge';
import addComposerItems from './addComposerItems';
import addLotteryToPost from './addLotteryToPost';
import addLotteryControls from './addLotteryControls';
import FailLotteryNotification from './components/FailLotteryNotification';
import FinishLotteryNotification from './components/FinishLotteryNotification';
import DrawLotteryNotification from './components/DrawLotteryNotification';

export * from './components';
export * from './models';

app.initializers.add('nodeloc/lottery', () => {
  addDiscussionBadge();
  addComposerItems();
  addLotteryToPost();
  addLotteryControls();
  app.notificationComponents.drawLottery = DrawLotteryNotification;
  app.notificationComponents.failLottery = FailLotteryNotification;
  app.notificationComponents.finishLottery = FinishLotteryNotification;
});

export { default as extend } from './extend';
