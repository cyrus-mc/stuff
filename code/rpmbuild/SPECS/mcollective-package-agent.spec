Name:           mcollective-package-agent
Version:	4.4.0
Release:        2%{?dist}
Summary:	Install and uninstall software packages

License:	ASL 2.0
URL:		http://puppetlabs.com/mcollective/introduction/
Group:		System Tools
Source0:	http://puppetlabs.com/mcollective/mcollective-package-agent-4.4.0.tar.gz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
#BuildRequires:
Requires:	mcollective-package-common
AutoReqProv:	no


%description
Install and uninstall software packages


%prep
%setup -q


%build
ls


%install
cp -rp $RPM_BUILD_DIR/%{name}-%{version} $RPM_BUILD_ROOT


%clean
# remove the build
rm -rf $RPM_BUILD_ROOT
# remove the extracted source
rm -rf $RPM_BUILD_DIR/%{name}-%{version}


%files
%defattr(-,root,root)
/usr/libexec/mcollective/mcollective/agent/package.rb


%changelog

