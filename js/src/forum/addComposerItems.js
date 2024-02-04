import app from 'flarum/forum/app';

import { extend } from 'flarum/common/extend';
import classList from 'flarum/common/utils/classList';
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';
import ReplyComposer from 'flarum/forum/components/ReplyComposer';

import CreateLotteryModal from './components/CreateLotteryModal';

export const addToComposer = (composer) => {
  composer.prototype.addLottery = function () {
    app.modal.show(CreateLotteryModal, {
      lottery: this.composer.fields.lottery,
      onsubmit: (lottery) => (this.composer.fields.lottery = lottery),
    });
  };

  // Add button to DiscussionComposer header
  extend(composer.prototype, 'headerItems', function (items) {
    const discussion = this.composer.body?.attrs?.discussion;
    const canStartLottery = discussion?.canStartLottery() ?? app.forum.canStartLottery();
    if (canStartLottery) {
      items.add(
        'lottery',
        <a className="ComposerBody-lottery" onclick={this.addLottery.bind(this)}>
          <span className={classList('LotteryLabel', !this.composer.fields.lottery && 'none')}>
            {app.translator.trans(`nodeloc-lottery.forum.composer_discussion.${this.composer.fields.lottery ? 'edit' : 'add'}_lottery`)}
          </span>
        </a>,
        1
      );
    }
  });

  extend(composer.prototype, 'data', function (data) {
    if (this.composer.fields.lottery) {
      data.lottery = this.composer.fields.lottery;
    }
  });
};

export default () => {
  addToComposer(DiscussionComposer);
};
