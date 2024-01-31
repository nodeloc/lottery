import Model from 'flarum/common/Model';

export default class LotteryVote extends Model {
  lottery = Model.hasOne('lottery');
  option = Model.hasOne('option');
  user = Model.hasOne('user');

  lotteryId = Model.attribute('lotteryId');
  optionId = Model.attribute('optionId');

  apiEndpoint() {
    return `/nodeloc/lottery/${this.lotteryId()}/vote`;
  }
}
