<?php
it(' has spaces  ', function () {
    $this->assertTrue(true);
});

test(' has spaces ', function () {

});

todo('has spaces ');
?>
-----
<?php
it('has spaces', function () {
    $this->assertTrue(true);
});

test('has spaces', function () {

});

todo('has spaces');
?>
