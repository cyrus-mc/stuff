Name:           mcollective-package-common
Version:	4.4.0
Release:        1%{?dist}
Summary:	Install and uninstall software packages

License:	ASL 2.0
URL:		http://puppetlabs.com/mcollective/introduction/
Group:		System Tools
Source0:	http://puppetlabs.com/mcollective/mcollective-package-common-4.4.0.tar.gz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
#BuildRequires:
Requires:	mcollective
AutoReqProv:	no


%description
Agent to do network tests from a mcollective host


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
/usr/libexec/mcollective/mcollective/agent/package.ddl
/usr/libexec/mcollective/mcollective/util/package
/usr/libexec/mcollective/mcollective/util/package/base.rb
/usr/libexec/mcollective/mcollective/util/package/packagehelpers.rb
/usr/libexec/mcollective/mcollective/util/package/puppetpackage.rb
/usr/libexec/mcollective/mcollective/util/package/yumHelper.py
/usr/libexec/mcollective/mcollective/util/package/yumHelper.pyc
/usr/libexec/mcollective/mcollective/util/package/yumHelper.pyo
/usr/libexec/mcollective/mcollective/util/package/yumpackage.rb


%changelog

