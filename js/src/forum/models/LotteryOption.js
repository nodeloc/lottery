import Model from 'flarum/common/Model';

export default class LotteryOption extends Model {
  operator_type = Model.attribute('operator_type');
  operator = Model.attribute('operator');
  operator_value = Model.attribute('operator_value');

  lottery = Model.hasOne('lottery');

  apiEndpoint() {
    return `/nodeloc/lottery/operator${this.exists ? `/${this.data.id}` : ''}`;
  }
}
