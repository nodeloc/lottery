import Model from 'flarum/common/Model';

export default class LotteryOption extends Model {
  answer = Model.attribute('answer');
  imageUrl = Model.attribute('imageUrl');
  voteCount = Model.attribute('voteCount');

  lottery = Model.hasOne('lottery');
  votes = Model.hasMany('votes');

  apiEndpoint() {
    return `/nodeloc/lottery/answers${this.exists ? `/${this.data.id}` : ''}`;
  }
}
