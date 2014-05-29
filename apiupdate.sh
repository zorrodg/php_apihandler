#!/bin/bash
# API Update Script
red="\033[0;31m"
blue="\033[0;36m"
green="\033[0;32m"
nc="\033[0m"
api_dir=$(pwd)/api/

#capture option flags
while getopts "ynd:" opt; do
  case $opt in
    y)
      # echo "-y was triggered!" >&2
      FLAG="yes" >&2
      ;;
    n)
      # echo "-n was triggered!" >&2
      FLAG="no" >&2
      ;;
    d)
      # Set API Directory
      api_dir=$OPTARG >&2
      ;;
    \?)
      echo "Invalid option: -$OPTARG" >&2
      exit
      ;;
  esac
done

echo "${green}Updating PHP API Handler in \"$api_dir\"..."

# Check composer vendor directory
if [[ ! -d vendor/ ]]; then
  echo "${red}ERROR:${nc} vendor directory not found."
  exit
fi

if [[ ! -d vendor/zorrodg/php_apihandler/api/ ]]; then
  echo "${red}ERROR:${nc} vendor apihandler directory not found."
  exit
fi

vendor_dir=vendor/zorrodg/php_apihandler/api/

if [[ ! -d $api_dir ]]; then
  if [[ "$FLAG" == "yes" ]]; then
    echo "${green}Creating $api_dir..."
    mkdir $api_dir
  elif [[ "$FLAG" == "no" ]]; then
    echo "${red}Target directory not created. Aborted."; 
    exit;
  else
    echo "${nc}Target directory not found. Do you want to create it?"
    select yn in "Yes" "No"; do
      case $yn in
        Yes) 
          echo "${green}Creating $api_dir...";
          mkdir $api_dir;
          break;;
        No) 
          echo "${red}Target directory not created. Aborted."; 
          exit;
      esac
    done
  fi
fi

# Check if directory has endpoints
if [[ -d ${api_dir}registered_endpoints/ ]]; then
  if [[ -n $(find ${api_dir}registered_endpoints/ -name "*.endpoints.php") ]]; then
    if [[ "$FLAG" == "yes" ]]; then
      echo "${green}Backing up endpoints...";
      for file in ${api_dir}registered_endpoints/*.endpoints.php; do
        filedest=${file##*/}
        if [[ ! "$filedest" == "myAPI.endpoints.php" ]]; then
          mv "$file" "${api_dir}${filedest%.php}.apbak"
        fi
      done
    else
      echo "${nc}Found endpoint files... Do you want to backup them?"
      select yn in "Yes" "No"; do
        case $yn in
          Yes) 
            echo "${green}Backing up endpoints...";
            for file in ${api_dir}registered_endpoints/*.endpoints.php; do
              filedest=${file##*/}
              if [[ ! "$filedest" == "myAPI.endpoints.php" ]]; then
                mv "$file" "${api_dir}${filedest%.php}.apbak"
              fi
            done
            break;;
          No) 
            echo "${red}Files in ${api_dir}registered_endpoints/ will not be backed up."; 
            break;;
        esac
      done
    fi
  fi
fi

# Check if api.config.php exists
if [[ -f ${api_dir}api.config.php ]]; then
  if [[ "$FLAG" == "yes" ]]; then
    echo "${green}Backing up api.config.php..."
    config=${api_dir}api.config.php
    mv "${config}" "${config%.php}.apbak"
  else
    echo "${nc}Found config file... Do you want to backup it?"
    select yn in "Yes" "No"; do
      case $yn in
        Yes) 
          echo "${green}Backing up api.config.php..."
          config=${api_dir}api.config.php
          mv "${config}" "${config%.php}.apbak"
          break;;
        No) 
          echo "${red}Config file will not be backed up."
          break;;
      esac
    done
  fi
fi

# Remove current API folders
rm -rf ${api_dir}cache
rm -rf ${api_dir}engine
rm -rf ${api_dir}oauth
rm -rf ${api_dir}registered_endpoints

# Move files from vendor folder to api folder
mv -f ${vendor_dir}* ${api_dir}
mv -f ${vendor_dir}.htaccess ${api_dir}.htaccess

# Changing config file for the one backed up
if [[ -f ${api_dir}api.config.apbak ]]; then
  config=${api_dir}api.config.apbak
  mv -f "${config}" "${config%.apbak}.php"
fi

# Returning backup to original folder
for file in ${api_dir}*.endpoints.apbak; do
  filedest=${file##*/}
  mv "$file" "${api_dir}registered_endpoints/${filedest%.apbak}.php"
done

# Ask if keep myAPI.endpoints file
if [[ -f ${api_dir}registered_endpoints/myAPI.endpoints.php ]]; then
  if [[ "$FLAG" == "no" ]]; then
    rm -rf ${vendor_dir%%/*}
  else
    echo "${nc}Keep myAPI endpoints example file?"
    select yn in "Yes" "No"; do
      case $yn in
        Yes)
          break;;
        No) 
          rm -rf ${api_dir}registered_endpoints/myAPI.endpoints.php
          break;;
      esac
    done
  fi
fi

#Keep vendor dir
if [[ "$FLAG" == "no" ]]; then
  rm -rf ${vendor_dir%%/*}
else
  echo "${nc}Keep vendor directory?"
  select yn in "Yes" "No"; do
    case $yn in
      Yes)
        break;;
      No) 
        rm -rf ${vendor_dir%%/*}
        break;;
    esac
  done
fi

# Updating composer dependencies
cd ${api_dir}
composer update

echo "${blue}Update completed"
echo
