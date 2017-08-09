<?php
/**
 * @file
 * Settings metadata for com.aghstrategies.contribmemberdiscount.
 * Copyright (C) 2016, AGH Strategies, LLC <info@aghstrategies.com>
 * Licensed under the GNU Affero Public License 3.0 (see LICENSE.txt)
 */
return array(
  'contribmemberdiscount_contribpages' => array(
    'group_name' => 'Membership Contribution Discount',
    'group' => 'contribmemberdiscount',
    'name' => 'contribmemberdiscount_contribpages',
    'type' => 'Array',
    'default' => NULL,
    'add' => '4.6',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Array of Pages to apply member discount on',
    'help_text' => 'civicontribute page(s) for which membership discount should be applied',
  ),
  'contribmemberdiscount_memtypes' => array(
    'group_name' => 'Membership Contribution Discount',
    'group' => 'contribmemberdiscount',
    'name' => 'contribmemberdiscount_memtypes',
    'type' => 'Array',
    'default' => NULL,
    'add' => '4.6',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Membership Types that qualify for the discount',
    'help_text' => 'Enter membership types that qualify for the discount',
  ),
  'contribmemberdiscount_memstatus' => array(
    'group_name' => 'Membership Contribution Discount',
    'group' => 'contribmemberdiscount',
    'name' => 'contribmemberdiscount_memstatus',
    'type' => 'Array',
    'default' => NULL,
    'add' => '4.6',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Array of qualifying membership statuss',
    'help_text' => 'Membership Statuss that qualify',
  ),
  'contribmemberdiscount_amount' => array(
    'group_name' => 'Membership Contribution Discount',
    'group' => 'contribmemberdiscount',
    'name' => 'contribmemberdiscount_amount',
    'type' => 'Integer',
    'default' => NULL,
    'add' => '4.6',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Discount Amount',
    'help_text' => 'Disount Amount',
  ),
);
