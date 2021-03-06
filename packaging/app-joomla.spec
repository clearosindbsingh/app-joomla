
Name: app-joomla
Epoch: 1
Version: 1.0.0
Release: 1%{dist}
Summary: **joomla_app_name**
License: GPL
Group: ClearOS/Apps
Packager: Xtreem Solution
Vendor: Xtreem Solution
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base
Requires: app-web-server
Requires: app-mariadb
Requires: unzip
Requires: zip

%description
**joomla_app_description**

%package core
Summary: **joomla_app_name** - Core
License: GPL
Group: ClearOS/Libraries
Requires: app-base-core
Requires: mod_authnz_external
Requires: mod_authz_unixgroup
Requires: mod_ssl
Requires: phpMyAdmin
Requires: app-flexshare-core

%description core
**joomla_app_description**

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/joomla
cp -r * %{buildroot}/usr/clearos/apps/joomla/

install -d -m 0775 %{buildroot}/var/clearos/joomla
install -d -m 0775 %{buildroot}/var/clearos/joomla/backup
install -d -m 0775 %{buildroot}/var/clearos/joomla/sites
install -d -m 0775 %{buildroot}/var/clearos/joomla/versions
install -D -m 0644 packaging/app-joomla.conf %{buildroot}/etc/httpd/conf.d/app-joomla.conf

%post
logger -p local6.notice -t installer 'app-joomla - installing'

%post core
logger -p local6.notice -t installer 'app-joomla-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/joomla/deploy/install ] && /usr/clearos/apps/joomla/deploy/install
fi

[ -x /usr/clearos/apps/joomla/deploy/upgrade ] && /usr/clearos/apps/joomla/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-joomla - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-joomla-core - uninstalling'
    [ -x /usr/clearos/apps/joomla/deploy/uninstall ] && /usr/clearos/apps/joomla/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/joomla/controllers
/usr/clearos/apps/joomla/htdocs
/usr/clearos/apps/joomla/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/joomla/packaging
%exclude /usr/clearos/apps/joomla/unify.json
%dir /usr/clearos/apps/joomla
%dir %attr(0775,webconfig,webconfig) /var/clearos/joomla
%dir %attr(0775,webconfig,webconfig) /var/clearos/joomla/backup
%dir %attr(0775,apache,apache) /var/clearos/joomla/sites
%dir %attr(0775,webconfig,webconfig) /var/clearos/joomla/versions
/usr/clearos/apps/joomla/deploy
/usr/clearos/apps/joomla/language
/usr/clearos/apps/joomla/libraries
/etc/httpd/conf.d/app-joomla.conf
