echo "Deleting test folder..."
rm -rf testing/
echo "Making folder..."
mkdir testing
cp -R . testing/
rm -rf testing/testing
cd testing
php -S 0.0.0.0:3000