FROM docker.io/centos

RUN /bin/yum -y update \
	&& /bin/yum -y install epel-release \
	&& /bin/yum -y install https://centos7.iuscommunity.org/ius-release.rpm \
	&& /bin/yum -y install python35u python35u-pip python35u-setuptools python35u-pip \
	&& /bin/ln -s /bin/python3.5 /bin/python3 \
	&& /bin/yum -y install python2-pylint-1.6.5-4.el7.noarch pylint-1.6.5-4.el7.noarch \
	&& /bin/yum -y install gcc cpp g++ gcc-c++ java-1.8.0-openjdk-headless java-1.8.0-openjdk-devel \
	&& /bin/pylint --reports=no --generate-rcfile > /etc/pylintrc \
	&& /sbin/useradd -c "Jobe User" jobe \
	&& /bin/chmod 770 /home/jobe \
	&& for x in jobe00 jobe01 jobe02 jobe03 jobe04 jobe05 jobe06 jobe07 jobe08 jobe09; do /sbin/useradd -c "${x} user" -g jobe -M -d /home/jobe $x; done

ADD runguard /usr/local/bin/runguard
WORKDIR /home/jobe

