import app from 'flarum/forum/app';

import { extend } from 'flarum/common/extend';
import CommentPost from 'flarum/forum/components/CommentPost';
import PostLottery from './components/PostLottery';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';

export default () => {
  extend(CommentPost.prototype, 'content', function (content) {
    const post = this.attrs.post;

    if ((!post.isHidden() || this.revealContent) && post.lottery()) {
        if (post.lottery()) {
          content.push(<PostLottery post={post} lottery={post.lottery()} />);
        }
    }
  });
};
