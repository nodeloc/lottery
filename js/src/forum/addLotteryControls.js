import app from 'flarum/forum/app';

import { extend } from 'flarum/common/extend';
import PostControls from 'flarum/forum/utils/PostControls';
import CreateLotteryModal from './components/CreateLotteryModal';
import Button from 'flarum/common/components/Button';

export default () => {
  const createLottery = (post) =>
    app.modal.show(CreateLotteryModal, {
      onsubmit: (data) =>
        app.store
          .createRecord('lottery')
          .save(
            {
              ...data,
              relationships: {
                post,
              },
            },
            {
              data: {
                include: 'options,lottery_participants',
              },
            }
          )
          .then((lottery) => {
            post.rawRelationship('lottery')?.push?.({ type: 'lottery', id: lottery.id() });
            return lottery;
          }),
    });

  // extend(PostControls, 'moderationControls', function (items, post) {
  //   if (!post.isHidden() && post.canStartLottery()) {
  //     items.add(
  //       'addLottery',
  //       <Button icon="fas fa-lottery" onclick={createLottery.bind(this, post)}>
  //         {app.translator.trans('nodeloc-lottery.forum.moderation.add')}
  //       </Button>
  //     );
  //   }
  // });
};
