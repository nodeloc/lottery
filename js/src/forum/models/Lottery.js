import Model from 'flarum/common/Model';

export default class Lottery extends Model {
  prizes = Model.attribute('prizes');
  hasEnded = Model.attribute('hasEnded');
  endDate = Model.attribute('endDate');

  price = Model.attribute('price');
  amount = Model.attribute('amount');
  min_participants = Model.attribute('min_participants');
  max_participants = Model.attribute('max_participants');

  enterCount = Model.attribute('enter_count');
  status = Model.attribute('status');
  canEnter = Model.attribute('canEnter');
  canEdit = Model.attribute('canEdit');
  canDelete = Model.attribute('canDelete');
  canSeeParticipants = Model.attribute('canSeeParticipants');
  canCancelEnter = Model.attribute('can_cancel_enter');

  options = Model.hasMany('options');
  participants = Model.hasMany('participants');
  lottery_participants = Model.hasMany('lottery_participants');

  apiEndpoint() {
    return `/nodeloc/lottery${this.exists ? `/${this.data.id}` : ''}`;
  }
}
