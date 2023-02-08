<?php
it('it can test', function () {
    $this->assertTrue(true);
});

it('it can also test')->assertTrue(true);

test('it does something', function () {
    $this->assertTrue(true);
});

test('it also works')->assertTrue(true);
?>
-----
<?php
it('can test', function () {
    $this->assertTrue(true);
});

it('can also test')->assertTrue(true);

it('does something', function () {
    $this->assertTrue(true);
});

it('also works')->assertTrue(true);
?>
