Access Switch 1 

en
conf t
hostname S1
vlan 10
name Meeting_RMs
vlan 20
name CS
vlan 40 
name Networking
vlan 60
name HR
spanning-tree vlan 20,40,60 priority 61440
interface g1/0/13
switchport mode access
switchport access vlan 40
interface g1/0/14
switchport mode access
switchport access vlan 60
interface range g1/0/1-12
switchport mode access
switchport access vlan 20
int range g1/0/15, g1/0/20
channel-group 1 mode desirable
no shut
int po1 
switchport mode trunk
switchport trunk allowed vlan 20,40,60
int range g1/0/16, g1/0/21
channel-group 2 mode desirable
no shut
int po2 
switchport mode trunk
switchport trunk allowed vlan 20,40,60
 



Access Switch 2

en
conf t
hostname S2
vlan 30
name Marketing
vlan 40
name Networking
vlan 60
name HR
spanning-tree vlan 30,40,60 priority 61440
interface g1/0/22
switchport mode access
switchport access vlan 40
interface g1/0/21
switchport mode access
switchport access vlan 60
interface range g1/0/1-10
switchport mode access
switchport access vlan 30
int range g1/0/11, g1/0/16
channel-group 1 mode desirable
no shut
int po1 
switchport mode trunk
switchport trunk allowed vlan 30,40,60
int range g1/0/12, g1/0/17
channel-group 2 mode desirable
no shut
int po2 
switchport mode trunk
switchport trunk allowed vlan 30,40,60
 



Access Switch 3

en
conf t
hostname S3
vlan 40
name Networking
vlan 50
name Solutions
vlan 60
name HR
vlan 80
name Servers
spanning-tree vlan 40,50,60,80 priority 61440
interface g1/0/4
switchport mode access
switchport access vlan 60
interface range g1/0/5-7
switchport mode access
switchport access vlan 40
interface range g1/0/8-10, g1/0/13-14
switchport mode access
switchport access vlan 50
interface range g1/0/1-3
switchport mode access
switchport access vlan 80
int range g1/0/11, g1/0/16
channel-group 1 mode desirable
no shut
int po1 
switchport mode trunk
switchport trunk allowed vlan 40,50,60,80
int range g1/0/12, g1/0/17
channel-group 2 mode desirable
no shut
int po2 
switchport mode trunk
switchport trunk allowed vlan 40,50,60,80
 



Access Switch 4 
wheres  vlan 10

en
conf t
hostname S4
vlan 10 
name Meeting_RMs
vlan 40
name Networking
vlan 60
name HR
vlan 70
name Managers
spanning-tree vlan 10,40,60,70 priority 61440
interface g1/0/17
switchport access vlan 10
interface g1/0/3
switchport mode access
switchport access vlan 40
interface range g1/0/8-9
switchport mode access
switchport access vlan 60
interface range g1/0/4-7
switchport mode access
switchport access vlan 70
int range g1/0/1, g1/0/13
channel-group 1 mode desirable
no shut
int po1 
switchport mode trunk
switchport trunk allowed vlan 40,60,70
int range g1/0/2, g1/0/14
channel-group 2 mode desirable
no shut
int po2 
switchport mode trunk
switchport trunk allowed vlan 40,60,70
 


CS1

en
conf t
hostname C1
vlan 10
name Meeting_RMs
vlan 20
name CS
vlan 30
name Marketing
vlan 40
name Networking
vlan 50
name Solutions
vlan 60
name HR
vlan 70
name Managers
vlan 80
name Servers
vlan 99
name Switch_MGM
spanning-tree vlan 1,10,20,30,40,50,60,70,80,99 priority 24576
int range g1/0/6, g1/0/11
channel-group 1 mode auto
spanning-tree guard root
int po1
switchport trunk allowed vlan 20,40,60
no shut
int range g1/0/5, g1/0/10
channel-group 2 mode auto
spanning-tree guard root
int po2
switchport trunk allowed vlan 30,40,60
no shut
int range g1/0/3, g1/0/9
channel-group 3 mode auto
spanning-tree guard root
int po3
switchport trunk allowed vlan 40,50,60,80
no shut
int range g1/0/4, g1/0/8
channel-group 4 mode auto
spanning-tree guard root
int po4
switchport trunk allowed vlan 40,60,70
spanning-tree guard root
no shut
int range g1/0/7, g1/0/12
no switchport
channel-group 5 mode on
switchport trunk allowed vlan 99
int po5
ip add 172.16.1.201 255.255.255.252
int vlan 10 
standby version 2
standby 1 ip 172.16.1.1
standby 1 priority 150
standby 1 preempt
int vlan 20
standby version 2
standby 2 ip 172.16.1.65
standby 2 priority 150
standby 2 preempt
int vlan 30
standby version 2
standby 3 ip 172.16.1.97
standby 3 priority 150
standby 2 preempt
int vlan 40
standby version 2
standby 4 ip 172.16.1.113
standby 4 priority 150
standby 4 preempt
int vlan 50
standby version 2
standby 5 ip 172.16.1.129
standby 5 priority 150
standby 5 preempt
int vlan 60
standby version 2
standby 6 ip 172.16.1.145
standby 6 priority 150
standby 6 preempt
int vlan 70
standvy version 2
standby 7 ip 

###########
ip routing
ip route 0.0.0.0 0.0.0.0 172.17.9.2
ip route 0.0.0.0 0.0.0.0 172.17.10.2

interface Vlan10
ip address 172.16.1.2 255.255.255.192
standby 10 ip 172.16.1.1
standby 10 priority 110
standby 10 preempt

interface Vlan20
ip address 172.16.1.66 255.255.255.224
standby 20 ip 172.16.1.65
standby 20 priority 110
standby 20 preempt

interface Vlan30
ip address 172.16.1.98 255.255.255.240
standby 30 ip 172.16.1.97
standby 30 priority 110
standby 30 preempt

interface Vlan40
ip address 172.16.1.114 255.255.255.240
standby 40 ip 172.16.1.113
standby 40 priority 110
standby 40 preempt

interface Vlan50
ip address 172.16.1.130 255.255.255.240
standby 50 ip 172.16.1.129
standby 50 priority 110
standby 50 preempt

interface Vlan60
ip address 172.16.1.146 255.255.255.240
standby 60 ip 172.16.1.145
standby 60 priority 110
standby 60 preempt

interface Vlan70
ip address 172.16.1.162 255.255.255.240
standby 70 ip 172.16.1.161
standby 70 priority 110
standby 70 preempt

interface Vlan80
ip address 172.16.1.194 255.255.255.248
standby 80 ip 172.16.1.193
standby 80 priority 110
standby 80 preempt

interface Vlan99
ip address 172.16.1.178 255.255.255.240
standby 99 ip 172.16.1.177
standby 99 priority 110
standby 99 preempt


interface g1/0/2
 switchport trunk encapsulation dot1q
 switchport mode trunk

interface g1/0/1
 switchport trunk encapsulation dot1q
 switchport mode trunk




















CS2

en
conf t
hostname C2
vlan 10
name Meeting_RMs
vlan 20
name CS
vlan 30
name Marketing
vlan 40
name Networking
vlan 50
name Solutions
vlan 60
name HR
vlan 70
name Managers
vlan 80
name Servers
vlan 99
name Switch_MGM
spanning-tree vlan 1,10,20,30,40,50,60,70,80,99 priority 32768
int range g1/0/5, g1/0/10
channel-group 1 mode auto
int po1
switchport trunk allowed vlan 20,40,60
no shut
int range g1/0/4, g1/0/11
channel-group 2 mode auto
int po2
switchport trunk allowed vlan 30,40,60
no shut
int range g1/0/6, g1/0/12
channel-group 3 mode auto
int po3
switchport trunk allowed vlan 40,50,60,80
no shut
int range g1/0/3, g1/0/9
channel-group 4 mode auto
int po4
switchport trunk allowed vlan 40,60,70
no shut
int range g1/0/7, g1/0/8
no switchport
channel-group 5 mode on
int po5
switchport trunk allowed vlan 99
ip add 172.16.1.202 255.255.255.252



###########
interface Vlan10
 ip address 172.16.1.3 255.255.255.192
 standby 10 ip 172.16.1.1
 standby 10 priority 90
 standby 10 preempt

interface Vlan20
 ip address 172.16.1.67 255.255.255.224
 standby 20 ip 172.16.1.65
 standby 20 priority 90
 standby 20 preempt

interface Vlan30
 ip address 172.16.1.99 255.255.255.240
 standby 30 ip 172.16.1.97
 standby 30 priority 90
 standby 30 preempt

interface Vlan40
 ip address 172.16.1.115 255.255.255.240
 standby 40 ip 172.16.1.113
 standby 40 priority 90
 standby 40 preempt

interface Vlan50
 ip address 172.16.1.131 255.255.255.240
 standby 50 ip 172.16.1.129
 standby 50 priority 90
 standby 50 preempt

interface Vlan60
 ip address 172.16.1.147 255.255.255.240
 standby 60 ip 172.16.1.145
 standby 60 priority 90
 standby 60 preempt

interface Vlan70
 ip address 172.16.1.163 255.255.255.240
 standby 70 ip 172.16.1.161
 standby 70 priority 90
 standby 70 preempt

interface Vlan80
 ip address 172.16.1.195 255.255.255.248
 standby 80 ip 172.16.1.193
 standby 80 priority 90
 standby 80 preempt

interface Vlan99
 ip address 172.16.1.179 255.255.255.240
 standby 99 ip 172.16.1.177
 standby 99 priority 90
 standby 99 preempt

interface g1/0/1
no switchport
ip add 172.16.1.205 255.255.255.252
interface g1/0/2
no switchport 
ip add 172.16.1.209 255.255.255.252
ip routing



###########
R1

interface GigabitEthernet0/1
 no shutdown

interface GigabitEthernet0/1.10
 encapsulation dot1Q 10
 ip address 172.16.1.254 255.255.255.192

interface GigabitEthernet0/1.20
 encapsulation dot1Q 20
 ip address 172.16.1.254 255.255.255.224

interface GigabitEthernet0/1.30
 encapsulation dot1Q 30
 ip address 172.16.1.254 255.255.255.240

interface GigabitEthernet0/1.40
 encapsulation dot1Q 40
 ip address 172.16.1.254 255.255.255.240

interface GigabitEthernet0/1.50
 encapsulation dot1Q 50
 ip address 172.16.1.254 255.255.255.240

interface GigabitEthernet0/1.60
 encapsulation dot1Q 60
 ip address 172.16.1.254 255.255.255.240

interface GigabitEthernet0/1.70
 encapsulation dot1Q 70
 ip address 172.16.1.254 255.255.255.240

interface GigabitEthernet0/1.80
 encapsulation dot1Q 80
 ip address 172.16.1.254 255.255.255.248

interface GigabitEthernet0/1.99
 encapsulation dot1Q 99
 ip address 172.16.1.254 255.255.255.240

interface GigabitEthernet0/0
 ip address 172.17.9.2 255.255.255.252
 no shutdown

ip route 0.0.0.0 0.0.0.0 172.17.9.1

interface GigabitEthernet0/0
 ip address 230.149.210.2 255.255.255.252
 ip nat outside
 no shutdown

interface GigabitEthernet0/1
 ip nat inside
 no shutdown

ip nat inside source static tcp 172.16.1.196 80 230.149.210.2 80
ip nat inside source static tcp 172.16.1.196 443 230.149.210.2 443

ip route 0.0.0.0 0.0.0.0 230.149.210.1








ip nat inside source static 172.16.10.149 230.149.210.9

interface GigabitEthernet1/0/1
ip nat outside
no shutdown

int g0/0/1
ip add 172.16.1.206 255.255.255.252
ip nat inside
no shut

int g0/0/0
ip add 172.16.1.218 255.255.255.252
ip nat inside
no shut


ip nat inside source static tcp 172.16.1.196 80 230.149.210.2 80
ip nat inside source static tcp 172.16.1.196 443 230.149.210.2 443

ip route 0.0.0.0 0.0.0.0 230.149.210.1















R2
interface GigabitEthernet0/1
 no shutdown

interface GigabitEthernet0/1.10
 encapsulation dot1Q 10
 ip address 172.16.1.253 255.255.255.192

interface GigabitEthernet0/1.20
 encapsulation dot1Q 20
 ip address 172.16.1.253 255.255.255.224

interface GigabitEthernet0/1.30
 encapsulation dot1Q 30
 ip address 172.16.1.253 255.255.255.240

interface GigabitEthernet0/1.40
 encapsulation dot1Q 40
 ip address 172.16.1.253 255.255.255.240

interface GigabitEthernet0/1.50
 encapsulation dot1Q 50
 ip address 172.16.1.253 255.255.255.240

interface GigabitEthernet0/1.60
 encapsulation dot1Q 60
 ip address 172.16.1.253 255.255.255.240

interface GigabitEthernet0/1.70
 encapsulation dot1Q 70
 ip address 172.16.1.253 255.255.255.240

interface GigabitEthernet0/1.80
 encapsulation dot1Q 80
 ip address 172.16.1.253 255.255.255.248

interface GigabitEthernet0/1.99
 encapsulation dot1Q 99
 ip address 172.16.1.253 255.255.255.240

interface GigabitEthernet0/0
 ip address 172.17.10.2 255.255.255.252
 no shutdown

ip route 0.0.0.0 0.0.0.0 172.17.10.1
