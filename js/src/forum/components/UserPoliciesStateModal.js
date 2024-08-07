import app from 'flarum/forum/app';
import humanTime from 'flarum/common/helpers/humanTime';
import Modal from 'flarum/common/components/Modal';
import sortByAttribute from '../../common/helpers/sortByAttribute';

/* global m */

export default class UserPoliciesStateModal extends Modal {
  title() {
    return app.translator.trans('fof-terms.forum.state-modal.title', {
      username: this.attrs.user.username(),
    });
  }

  className() {
    return 'UserPoliciesStateModal Modal--medium';
  }

  content() {
    return m(
      '.Modal-body',
      m(
        'ul',
        sortByAttribute(app.store.all('fof-terms-policies')).map((policy) => {
          const state = this.attrs.user.fofTermsPoliciesState()[policy.id()];

          return m('li', [
            policy.name() + ': ',
            state && state.accepted_at
              ? app.translator.trans('fof-terms.forum.state-modal.accepted-at', {
                  date: humanTime(state.accepted_at),
                })
              : app.translator.trans('fof-terms.forum.state-modal.not-accepted'),
          ]);
        })
      )
    );
  }
}
