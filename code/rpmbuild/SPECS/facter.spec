Name:           facter
Version:	2.4.3
Release:        1%{?dist}
Summary:	Ruby module for collecting simple facts about a host operating system

License:	ASL 2.0
URL:		http://www.puppetlabs.com/puppet/related-projects/facter
Source0:	https://downloads.puppetlabs.com/facter/facter-2.4.3.tar.gz
Group:		System Environment/Base

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
#BuildRequires:
Requires:	pe-ruby
AutoReqProv:	no


%description
Ruby module for collecting simple facts about a host Operating System.
Some of the facts are preconfigured, such as the hostname and the operating system.
Additional facts can be added through simple Ruby scripts.


%prep
%setup -q


%build
ls


%install
/opt/puppet/bin/ruby install.rb --destdir=$RPM_BUILD_ROOT


%clean
# remove the build
rm -rf $RPM_BUILD_ROOT
# remove the extracted source
rm -rf $RPM_BUILD_DIR/%{name}-%{version}


%files
%defattr(-,root,root)
/opt/puppet


%changelog

