import app from 'flarum/forum/app';

import { extend } from 'flarum/common/extend';
import CommentPost from 'flarum/forum/components/CommentPost';
import PostLottery from './components/PostLottery';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';

export default () => {
  extend(CommentPost.prototype, 'content', function (content) {
    const post = this.attrs.post;

    if ((!post.isHidden() || this.revealContent) && post.lottery()) {
      for (const lottery of post.lottery()) {
        if (lottery) {
          content.push(<PostLottery post={post} lottery={lottery} />);
        }
      }
    }
  });

  extend(CommentPost.prototype, 'oninit', function () {
    this.subtree.check(() => {
      const lottery = this.attrs.post.lottery();

      const checks = lottery?.map?.(
        (lottery) =>
          lottery && [
            lottery.data?.attributes,
            lottery.options().map?.((option) => option?.data?.attributes),
            lottery.myVotes().map?.((vote) => vote.option()?.id()),
          ]
      );

      return JSON.stringify(checks);
    });
  });

  extend(DiscussionPage.prototype, 'oncreate', function () {
    if (app.pusher) {
      app.pusher.then((binding) => {
        // We will listen for updates to all lottery and options
        // Even if that model is not in the current discussion, it doesn't really matter
        binding.channels.main.bind('updatedLotteryOptions', (data) => {
          const lottery = app.store.getById('lottery', data['lotteryId']);

          if (lottery) {
            lottery.pushAttributes({
              voteCount: data['lotteryVoteCount'],
            });

            // Not redrawing here, as the option below should trigger the redraw already
          }

          const changedOptions = data['options'];

          for (const optionId in changedOptions) {
            const option = app.store.getById('lottery_options', optionId);

            if (option && option.voteCount() !== undefined) {
              option.pushAttributes({
                voteCount: changedOptions[optionId],
              });
            }
          }

          m.redraw();
        });
      });
    }
  });

  extend(DiscussionPage.prototype, 'onremove', function () {
    if (app.pusher) {
      app.pusher.then((binding) => {
        binding.channels.main.unbind('updatedLotteryOptions');
      });
    }
  });
};
