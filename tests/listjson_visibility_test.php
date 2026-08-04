<?php

class DB
{
}

class FakeResult
{
    public $rows;
    public $position = 0;

    public function __construct($rows)
    {
        $this->rows = array_values($rows);
    }
}

class FakeDB extends DB
{
    private $groups;
    private $linksByGroup;

    public function __construct($groups, $linksByGroup)
    {
        $this->groups = $groups;
        $this->linksByGroup = $linksByGroup;
    }

    public function query($sql)
    {
        if (strpos($sql, 'FROM `lylme_groups`') !== false) {
            return new FakeResult($this->groups);
        }

        if (preg_match('/WHERE `group_id` = (\d+)/', $sql, $matches)) {
            $groupId = (int)$matches[1];
            return new FakeResult(isset($this->linksByGroup[$groupId]) ? $this->linksByGroup[$groupId] : []);
        }

        return false;
    }

    public function fetch($result)
    {
        if ($result->position >= count($result->rows)) {
            return false;
        }

        return $result->rows[$result->position++];
    }
}

require dirname(__DIR__) . '/include/lists.php';

function assertSameValue($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . "\nExpected: " . json_encode($expected) . "\nActual: " . json_encode($actual) . "\n");
        exit(1);
    }
}

function groupIds($groups)
{
    return array_map(function ($group) {
        return $group['id'];
    }, $groups);
}

function itemIds($groups, $groupId)
{
    foreach ($groups as $group) {
        if ($group['id'] === $groupId) {
            return array_map(function ($item) {
                return $item['id'];
            }, $group['items']);
        }
    }

    return [];
}

$groups = [
    ['group_id' => 1, 'group_name' => 'Public', 'group_icon' => '', 'group_status' => 1, 'group_pwd' => 0],
    ['group_id' => 2, 'group_name' => 'Protected', 'group_icon' => '', 'group_status' => 1, 'group_pwd' => '9'],
    ['group_id' => 3, 'group_name' => 'Disabled', 'group_icon' => '', 'group_status' => 0, 'group_pwd' => 0],
];

$linksByGroup = [
    1 => [
        ['id' => 101, 'name' => 'Public link', 'icon' => '', 'url' => 'https://example.com/public', 'link_status' => 1, 'link_pwd' => 0],
        ['id' => 102, 'name' => 'Protected link', 'icon' => '', 'url' => 'https://example.com/protected', 'link_status' => 1, 'link_pwd' => '7'],
        ['id' => 103, 'name' => 'Disabled link', 'icon' => '', 'url' => 'https://example.com/disabled', 'link_status' => 0, 'link_pwd' => 0],
    ],
    2 => [
        ['id' => 201, 'name' => 'Protected group link', 'icon' => '', 'url' => 'https://example.com/group', 'link_status' => 1, 'link_pwd' => '99'],
    ],
    3 => [
        ['id' => 301, 'name' => 'Disabled group link', 'icon' => '', 'url' => 'https://example.com/disabled-group', 'link_status' => 1, 'link_pwd' => 0],
    ],
];

$_SESSION['list'] = [];
$DB = new FakeDB($groups, $linksByGroup);
$result = listjson();
assertSameValue([1], groupIds($result), 'Unauthenticated users must not receive protected or disabled groups.');
assertSameValue([101], itemIds($result, 1), 'Unauthenticated users must receive only public, enabled links.');

$_SESSION['list'] = [7];
$DB = new FakeDB($groups, $linksByGroup);
$result = listjson();
assertSameValue([101, 102], itemIds($result, 1), 'An authorized link password must reveal only its matching links.');

$_SESSION['list'] = [9];
$DB = new FakeDB($groups, $linksByGroup);
$result = listjson();
assertSameValue([1, 2], groupIds($result), 'An authorized group password must reveal its matching group.');
assertSameValue([201], itemIds($result, 2), 'An authorized group must reveal its enabled links.');

echo "listjson visibility tests passed\n";
