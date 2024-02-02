import Model from 'flarum/common/Model';

export default class LotteryParticipants extends Model {
  lottery = Model.hasOne('lottery');
  user = Model.hasOne('user');

  lotteryId = Model.attribute('lotteryId');

  apiEndpoint() {
    return `/nodeloc/lottery/${this.lotteryId()}/vote`;
  }
}
