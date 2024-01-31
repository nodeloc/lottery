import app from 'flarum/forum/app';

import addDiscussionBadge from './addDiscussionBadge';
import addComposerItems from './addComposerItems';
import addLotteryToPost from './addLotteryToPost';
import addLotteryControls from './addLotteryControls';

export * from './components';
export * from './models';

app.initializers.add('nodeloc/lottery', () => {
  addDiscussionBadge();
  addComposerItems();
  addLotteryToPost();
  addLotteryControls();
});

export { default as extend } from './extend';
