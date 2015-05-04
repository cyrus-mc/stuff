Name:           puppet
Version:	3.7.5
Release:        1%{?dist}
Summary:	A network tool for managing many disparate systems

License:	ASL 2.0
URL:		http://puppetlabs.com
Source0:	https://downloads.puppetlabs.com/puppet/puppet-3.7.5.tar.gz
Group:		System Environment/Base

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)
#BuildRequires:
Requires:	pe-ruby, hiera, facter
AutoReqProv:	no


%description
Puppet lets you centrally manage every important aspect of your sytems using a
cross-platform specification language that manages all the separate elements normally
aggregated in differet files, like users, cron jobs, and hosts, along with obviously
discrete elements like packages, services, and files.


%prep
%setup -q


%build
ls


%install
/opt/puppet/bin/ruby install.rb --destdir=$RPM_BUILD_ROOT
#generate stub puppet.conf file
cat << __EOF__ > $RPM_BUILD_ROOT/etc/puppet/puppet.conf
[main]
    # The Puppet log directory.
    # The default value is '$vardir/log'.
    logdir = /var/log/puppet

    # Where Puppet PID files are kept.
    # The default value is '$vardir/run'.
    rundir = /var/run/puppet

    # Where SSL certificates are kept.
    # The default value is '$confdir/ssl'.
    ssldir = $vardir/ssl

[agent]
    # The file in which puppetd stores a list of the classes
    # associated with the retrieved configuratiion.  Can be loaded in
    # the separate ``puppet`` executable using the ``--loadclasses``
    # option.
    # The default value is '$confdir/classes.txt'.
    classfile = $vardir/classes.txt

    # Where puppetd caches the local configuration.  An
    # extension indicating the cache format is added automatically.
    # The default value is '$confdir/localconfig'.
    localconfig = $vardir/localconfig
__EOF__


%clean
# remove the build
rm -rf $RPM_BUILD_ROOT
# remove the extracted source
rm -rf $RPM_BUILD_DIR/%{name}-%{version}


%files
%defattr(-,root,root)
/opt/puppet
%config /etc/puppet/auth.conf
%config /etc/puppet/puppet.conf


%changelog

