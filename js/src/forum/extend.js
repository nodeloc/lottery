import Extend from 'flarum/common/extenders';
import Post from 'flarum/common/models/Post';
import Forum from 'flarum/common/models/Forum';
import Discussion from 'flarum/common/models/Discussion';
import Lottery from './models/Lottery';
import LotteryOption from './models/LotteryOption';
import LotteryVote from './models/LotteryVote';

export default [
  new Extend.Store().add('lottery', Lottery).add('lottery_options', LotteryOption).add('lottery_votes', LotteryVote),
  new Extend.Model(Post).hasMany('lottery').attribute('canStartLottery'),
  new Extend.Model(Forum).attribute('canStartLottery'),
  new Extend.Model(Discussion).attribute('hasLottery').attribute('canStartLottery'),
];
