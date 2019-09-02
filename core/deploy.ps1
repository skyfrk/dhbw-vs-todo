# Settings
$env = "dev"
$organisation = "dhbw"
$product = "todocore"
$location = "westeurope"
$rgname = "$organisation-$product-$env-rg"
$rname = $organisation + $product + $env
$aspSku = "FREE"
$sqlRootPassword = ""
$sqlDatabase = "todo"

# Create Resource Group
az group create --l $location -n $rgname
az configure --defaults group=$rgname

# Create App Service Plan
az appservice plan create -n "$($rname)asp" --sku $aspSku

# Create Web App
az webapp create --plan "$($rname)asp" -n "$($rname)wa"
az webapp config set -n "$($rname)wa" --php-version "7.3"

# Create Container Instance Group with MariaDB Container
az container create `
    --image mariadb `
    --cpu 1 `
    --dns-name-label "$($rname)cg" `
    --secure-environment-variables MYSQL_ROOT_PASSWORD=$sqlRootPassword MYSQL_DATABASE=$sqlDatabase `
    --ip-address Public `
    --location $location `
    --memory 0.5 `
    --name "$($rname)cg" `
    --ports 3306 `
    --restart-policy OnFailure