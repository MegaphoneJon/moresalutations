<?php

require_once 'moresalutations.civix.php';
// phpcs:disable
use CRM_Moresalutations_ExtensionUtil as E;
// phpcs:enable

function moresalutations_civicrm_tokens(&$tokens) {
  $tokens['moresalutations']['moresalutations.joint_casual'] = 'Joint greeting with optional nicknames';
}

function moresalutations_civicrm_tokenValues(&$values, $cids, $job = NULL, $tokens = array(), $context = NULL) {
  if (!isset($tokens['moresalutations'])) {
    return;
  }

  $contacts = \Civi\Api4\Contact::get(FALSE)
    ->addSelect('first_name', 'nick_name', 'last_name')
    ->addWhere('id', 'IN', $cids)
    ->execute()
    ->indexBy('id');
  // Look up the spouses. Not every contact will have one.
  $spouses = \Civi\Api4\RelationshipCache::get(FALSE)
    ->addSelect('near_contact_id', 'far_contact_id.first_name', 'far_contact_id.last_name', 'far_contact_id.nick_name')
    ->addWhere('relationship_type_id.label_a_b', '=', 'Spouse of')
    ->addWhere('near_contact_id', 'IN', $cids)
    ->execute()
    ->indexBy('near_contact_id');
  foreach ($contacts as $cid => $contact) {
    // Use nickname if it exists.
    $greetingName = $contacts[$cid]['nick_name'] ?? $contacts[$cid]['first_name'];
    // Has a spouse.
    if ($spouses[$cid] ?? FALSE) {
      // Use spouse nickname if it exists.
      $spouseGreetingName = $spouses[$cid]['far_contact_id.nick_name'] ?? $spouses[$cid]['far_contact_id.first_name'];
      $sharedLastName = $spouses[$cid]['far_contact_id.last_name'] === $contacts[$cid]['last_name'];
      if ($sharedLastName) {
        $values[$cid]['moresalutations.joint_casual'] = $greetingName . ' and ' . $spouseGreetingName . ' ' . $contacts[$cid]['last_name'];
      }
      else {
        $values[$cid]['moresalutations.joint_casual'] = $greetingName . ' ' . $contacts[$cid]['last_name'] . ' and ' . $spouseGreetingName . ' ' . $spouses[$cid]['far_contact_id.last_name'];
      }
    }
    else {
      $values[$cid]['moresalutations.joint_casual'] = $greetingName . ' ' . $contacts[$cid]['last_name'];
    }
  }

}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function moresalutations_civicrm_config(&$config): void {
  _moresalutations_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function moresalutations_civicrm_install(): void {
  _moresalutations_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function moresalutations_civicrm_enable(): void {
  _moresalutations_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function moresalutations_civicrm_preProcess($formName, &$form): void {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function moresalutations_civicrm_navigationMenu(&$menu): void {
//  _moresalutations_civix_insert_navigation_menu($menu, 'Mailings', [
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ]);
//  _moresalutations_civix_navigationMenu($menu);
//}
