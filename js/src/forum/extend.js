import Extend from 'flarum/common/extenders';
import Post from 'flarum/common/models/Post';
import Forum from 'flarum/common/models/Forum';
import Discussion from 'flarum/common/models/Discussion';
import Lottery from './models/Lottery';
import LotteryOption from './models/LotteryOption';
import LotteryParticipants from './models/LotteryParticipants';

export default [
  new Extend.Store().add('lottery', Lottery).add('lottery_options', LotteryOption).add('lottery_participants', LotteryParticipants),
  new Extend.Model(Post).hasOne('lottery').attribute('canStartLottery'),
  new Extend.Model(Forum).attribute('canStartLottery'),
  new Extend.Model(Discussion).attribute('hasLottery').attribute('canStartLottery'),
];
