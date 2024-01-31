import Model from 'flarum/common/Model';

export default class PollOption extends Model {
  answer = Model.attribute('answer');
  imageUrl = Model.attribute('imageUrl');
  voteCount = Model.attribute('voteCount');

  poll = Model.hasOne('polls');
  votes = Model.hasMany('votes');

  apiEndpoint() {
    return `/nodeloc/lottery/answers${this.exists ? `/${this.data.id}` : ''}`;
  }
}
