// @ts-ignore
import app from 'flarum/admin/app';

app.initializers.add('nodeloc/lottery', () => {
  app.extensionData
    .for('nodeloc-lottery')
    .registerSetting({
      setting: 'nodeloc-lottery.allowOptionImage',
      type: 'switch',
      label: app.translator.trans('nodeloc-lottery.admin.settings.allow_option_image'),
    })
    .registerSetting({
      setting: 'nodeloc-lottery.optionsColorBlend',
      type: 'switch',
      label: app.translator.trans('nodeloc-lottery.admin.settings.options_color_blend'),
      help: app.translator.trans('nodeloc-lottery.admin.settings.options_color_blend_help'),
    })
    .registerSetting({
      setting: 'nodeloc-lottery.maxOptions',
      type: 'number',
      label: app.translator.trans('nodeloc-lottery.admin.settings.max_options'),
      min: 1,
    })
    .registerPermission(
      {
        icon: 'fas fa-signal',
        label: app.translator.trans('nodeloc-lottery.admin.permissions.seeParticipants'),
        permission: 'lottery.seeParticipants',
        allowGuest: true,
      },
      'view'
    )
    .registerPermission(
      {
        icon: 'fas fa-signal',
        label: app.translator.trans('nodeloc-lottery.admin.permissions.start'),
        permission: 'discussion.lottery.start',
      },
      'start'
    )
    .registerPermission(
      {
        icon: 'fas fa-pencil-alt',
        label: app.translator.trans('nodeloc-lottery.admin.permissions.self_edit'),
        permission: 'lottery.selfEdit',
      },
      'start'
    )
    .registerPermission(
      {
        icon: 'fas fa-pencil-alt',
        label: app.translator.trans('nodeloc-lottery.admin.permissions.self_post_edit'),
        permission: 'lottery.selfPostEdit',
      },
      'start'
    )
    .registerPermission(
      {
        icon: 'fas fa-signal',
        label: app.translator.trans('nodeloc-lottery.admin.permissions.enter'),
        permission: 'discussion.lottery.enter',
      },
      'reply'
    )
    .registerPermission(
      {
        icon: 'fas fa-signal',
        label: app.translator.trans('nodeloc-lottery.admin.permissions.canCancelEnter'),
        permission: 'lottery.cancelEnter',
      },
      'reply'
    )
    .registerPermission(
      {
        icon: 'fas fa-pencil-alt',
        label: app.translator.trans('nodeloc-lottery.admin.permissions.moderate'),
        permission: 'discussion.lottery.moderate',
      },
      'moderate'
    );
});
