# Settings
$env = "dev"
$organisation = "dhbw"
$product = "todofrontend"
$location = "westeurope"
$rgname = "$organisation-$product-$env-rg"
$rname = $organisation + $product + $env
$aspSku = "FREE"

# Create Resource Group
az group create --l $location -n $rgname
az configure --defaults group=$rgname

# Create App Service Plan
az appservice plan create -n "$($rname)asp" --sku $aspSku

# Create Web App
az webapp create --plan "$($rname)asp" -n "$($rname)wa"