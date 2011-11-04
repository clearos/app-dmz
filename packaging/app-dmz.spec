
Name: app-dmz
Group: ClearOS/Apps
Version: 6.1.0.beta2
Release: 1%{dist}
Summary: DMZ Firewall
License: GPLv3
Packager: ClearFoundation
Vendor: ClearFoundation
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = %{version}-%{release}
Requires: app-base
Requires: app-network

%description
Adding a DMZ to host higher-risk external services like web, email and VoIP can increase the security of your Local Area Network.

%package core
Summary: DMZ Firewall - APIs and install
Group: ClearOS/Libraries
License: LGPLv3
Requires: app-base-core
Requires: app-firewall-core
Requires: app-network-core

%description core
Adding a DMZ to host higher-risk external services like web, email and VoIP can increase the security of your Local Area Network.

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
%exclude /usr/clearos/apps/dmz/tests
%dir /usr/clearos/apps/dmz
/usr/clearos/apps/dmz/deploy
/usr/clearos/apps/dmz/language
/usr/clearos/apps/dmz/libraries
