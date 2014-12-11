
Name: app-dmz
Epoch: 1
Version: 2.0.5
Release: 1%{dist}
Summary: DMZ Firewall
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base
Requires: app-network

%description
The DMZ app provides firewall tools for systems in the de-militarized zone.  Isolating high risk external services such as web, e-mail and VoIP system can improve the security of your network.

%package core
Summary: DMZ Firewall - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: app-firewall >= 1:1.5.19
Requires: app-network-core

%description core
The DMZ app provides firewall tools for systems in the de-militarized zone.  Isolating high risk external services such as web, e-mail and VoIP system can improve the security of your network.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/dmz
cp -r * %{buildroot}/usr/clearos/apps/dmz/


%post
logger -p local6.notice -t installer 'app-dmz - installing'

%post core
logger -p local6.notice -t installer 'app-dmz-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/dmz/deploy/install ] && /usr/clearos/apps/dmz/deploy/install
fi

[ -x /usr/clearos/apps/dmz/deploy/upgrade ] && /usr/clearos/apps/dmz/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-dmz - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-dmz-core - uninstalling'
    [ -x /usr/clearos/apps/dmz/deploy/uninstall ] && /usr/clearos/apps/dmz/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/dmz/controllers
/usr/clearos/apps/dmz/htdocs
/usr/clearos/apps/dmz/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/dmz/packaging
%dir /usr/clearos/apps/dmz
/usr/clearos/apps/dmz/deploy
/usr/clearos/apps/dmz/language
/usr/clearos/apps/dmz/libraries
