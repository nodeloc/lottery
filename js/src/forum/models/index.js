import Lottery from './Lottery';
import LotteryOption from './LotteryOption';
import LotteryVote from './LotteryVote';

export const models = {
  Poll: Lottery,
  PollOption: LotteryOption,
  PollVote: LotteryVote,
};
