{!! $translator->trans('nodeloc-lottery.email.body.fail', [
    '{recipient_display_name}' => $user->display_name,
    '{discussion_title}' => $blueprint->discussion->title,
    '{discussion_url}' => $url->to('forum')->route('discussion', ['id' => $blueprint->discussion->id]),
]) !!}
