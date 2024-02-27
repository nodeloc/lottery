import app from 'flarum/forum/app';

import { extend } from 'flarum/common/extend';
import Badge from 'flarum/common/components/Badge';
import DiscussionList from 'flarum/forum/components/DiscussionList';
import Discussion from 'flarum/common/models/Discussion';

export default () => {
  extend(DiscussionList.prototype, 'requestParams', (params) => {
    params.include.push('firstPost.lottery');
  });

  extend(Discussion.prototype, 'badges', function (badges) {
    if (this.hasLottery()) {
      badges.add(
        'lottery',
        Badge.component({
          type: 'lottery',
          label: app.translator.trans('nodeloc-lottery.forum.tooltip.badge'),
          icon: 'fas fa-gift',
        }),
        5
      );
    }
  });
};
