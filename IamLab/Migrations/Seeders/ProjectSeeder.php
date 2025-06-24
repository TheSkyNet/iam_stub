<?php

namespace IamLab\Migrations\Seeders;

use Phalcon\Migrations\Mvc\Model\Migration;
use IamLab\Model\Project;

class ProjectSeeder extends Migration
{
    public function run(): void
    {
        $projects = [
            [
                'img' => '/files/hallmark-prev.fw.png',
                'title' => 'Hallmark Woodworking - 3D Cabinet Design Platform',
                'body' => "# Custom Cabinet Design Solution

## Revolutionizing Woodworking Manufacturing
k
Our innovative 3D cabinet design platform transforms traditional woodworking into a digital-first experience. This enterprise solution enables real-time design visualization, automated quoting, and precision manufacturing specifications.

The platform reduced design time by 60% while increasing accuracy to 99.9%. Custom reporting tools provide detailed material requirements and cut lists, streamlining the entire manufacturing process.

### Key Features:
- Real-time 3D visualization
- Automated quote generation
- Material optimization algorithms
- CNC machine integration
- Inventory management
- Customer approval portal

### Technologies Used:
- React.js
- Three.js
- PHP/Laravel
- MySQL
- Azure Cloud
- REST API
- Linux/Docker

*Keywords: Woodworking Software, Manufacturing Automation, 3D Design, Custom Cabinets*",
                'type' => 'project'
            ],
            [
                'img' => '/files/Game-yetu.fw.png',
                'title' => 'Game Yetu - Social Gaming Platform',
                'body' => "# Next-Gen Gaming Community Platform

## Where Gamers Unite

Game Yetu revolutionizes the gaming community experience by combining social networking with game statistics tracking and tournament organization. This platform serves over 50,000 active users across multiple gaming titles.

Our innovative matchmaking algorithm and reputation system created a thriving community with 40% month-over-month growth in user engagement.

### Platform Features:
- Cross-platform statistics tracking
- Automated tournament bracketing
- Real-time match results
- Social networking integration
- Achievement system
- Custom game overlays

### Tech Stack:
- Vue.js
- Node.js
- MongoDB
- WebSocket
- AWS
- Rust (performance modules)
- Kubernetes

*Keywords: Gaming Platform, eSports, Social Gaming, Tournament Management*",
                'type' => 'project'
            ],
            [
                'img' => '/files/Slimmables-Home.png',
                'title' => 'Slimmables - Fitness Technology Platform',
                'body' => "# AI-Powered Fitness Journey Platform

## Personalized Wellness Revolution

Slimmables transforms fitness tracking through AI-powered personalization and real-time coaching. Our platform processes over 1 million workout sessions monthly, providing actionable insights and adaptive training programs.

Machine learning algorithms analyze user performance to create truly personalized workout plans, resulting in 78% better adherence rates compared to traditional programs.

### Core Features:
- AI workout adaptation
- Nutrition tracking
- Progress visualization
- Social challenges
- Virtual coaching
- Wearable integration

### Technology Stack:
- React Native
- Python/Django
- PostgreSQL
- TensorFlow
- Google Cloud
- GraphQL
- Microservices

*Keywords: Fitness Tech, AI Coaching, Health Analytics, Wellness Platform*",
                'type' => 'project'
            ],
            [
                'img' => '/files/moonworkes-profile.PNG',
                'title' => 'Moonworks - Creative Project Management',
                'body' => "# Creative Workflow Management Platform

## Empowering Creative Teams

Moonworks streamlines creative project management with intelligent workflow automation and resource allocation. The platform handles over 10,000 creative projects annually for agencies and in-house teams.

Our innovative approach to creative project management increased team productivity by 45% while reducing revision cycles by 60%.

### Platform Features:
- Visual kanban boards
- Resource forecasting
- Automated workflows
- Asset management
- Client collaboration
- Time tracking
- Performance analytics

### Technical Stack:
- Mithril.js
- PHP/Phalcon
- MySQL
- Redis
- AWS
- REST API
- Docker

*Keywords: Project Management, Creative Workflow, Team Collaboration, Resource Planning*",
                'type' => 'project'
            ],
            [
                'img' => '/files/benito-pre.png',
                'title' => 'Benito - E-commerce Management System',
                'body' => "# Advanced E-commerce Management Platform

## Streamlining Online Retail Operations

Benito revolutionizes e-commerce management with an intelligent inventory and order processing system. The platform handles over $50M in annual transactions while maintaining 99.99% uptime.

Our scalable solution reduced order processing time by 75% and improved inventory accuracy to 99.8%.

### Core Features:
- Real-time inventory tracking
- Automated order processing
- Multi-warehouse management
- Dynamic pricing engine
- Returns automation
- Analytics dashboard

### Tech Stack:
- PHP/Symfony
- Vue.js
- PostgreSQL
- ElasticSearch
- RabbitMQ
- Docker
- AWS

*Keywords: E-commerce Platform, Inventory Management, Order Processing, Retail Tech*",
                'type' => 'project'
            ],
            [
                'img' => '/files/engineroomsports.jpg',
                'title' => 'Engine Room Sports - Athletics Management Platform',
                'body' => "# Sports Team Management Solution

## Empowering Athletic Excellence

Engine Room Sports transforms sports team management with comprehensive player tracking, performance analytics, and event scheduling. Currently serving 200+ sports organizations.

The platform increased training efficiency by 35% and reduced administrative overhead by 50%.

### Platform Features:
- Player performance tracking
- Team scheduling system
- Tournament management
- Injury monitoring
- Equipment inventory
- Recruitment portal
- Analytics suite

### Technologies Used:
- React.js
- Node.js
- MongoDB
- WebSockets
- Azure
- Machine Learning
- Mobile Apps

*Keywords: Sports Management, Team Organization, Athletic Performance, Sports Analytics*",
                'type' => 'project'
            ],
            [
                'img' => '/files/saharalondon.png',
                'title' => 'Sahara London - Fashion E-commerce Platform',
                'body' => "# Luxury Fashion E-commerce Solution

## Digital Fashion Innovation

Sahara London redefines online fashion retail with an AI-powered recommendation engine and virtual fitting room technology. The platform processes over 10,000 orders daily.

Our innovative approach increased conversion rates by 45% and reduced returns by 30%.

### Key Features:
- AI style recommendations
- Virtual try-on system
- Inventory forecasting
- Social shopping
- Loyalty program
- Mobile-first design
- International shipping

### Technical Stack:
- Next.js
- Python/Flask
- MySQL
- TensorFlow
- AWS
- GraphQL
- Stripe Integration

*Keywords: Fashion Tech, E-commerce, Virtual Fitting, AI Recommendations*",
                'type' => 'project'
            ],
            [
                'img' => '/files/indiog-prvuw.png',
                'title' => 'Indigo - Digital Asset Management',
                'body' => "# Enterprise Asset Management Platform

## Revolutionizing Digital Asset Control

Indigo transforms digital asset management with AI-powered organization and real-time collaboration features. Managing over 5 million assets for enterprise clients.

The platform reduced asset search time by 80% while improving team collaboration efficiency by 60%.

### Platform Features:
- AI asset tagging
- Version control
- Rights management
- Team collaboration
- Asset analytics
- Cloud storage
- API integration

### Technology Stack:
- Angular
- Java Spring
- PostgreSQL
- Redis
- GCP
- Kubernetes
- AI/ML Pipeline

*Keywords: Digital Asset Management, Enterprise Software, Content Organization, Team Collaboration*",
                'type' => 'project'
            ],
            [
                'img' => '/files/jj-prevu.png',
                'title' => 'JJ Analytics - Business Intelligence Platform',
                'body' => "# Smart Business Analytics Solution

## Data-Driven Decision Making

JJ Analytics provides real-time business intelligence with predictive analytics and automated reporting. Processing over 1TB of data daily for actionable insights.

Helped clients achieve average 40% improvement in decision-making efficiency.

### Core Features:
- Real-time analytics
- Predictive modeling
- Custom dashboards
- Data visualization
- Automated reporting
- Integration APIs
- Alert system

### Tech Stack:
- React
- Python
- Apache Spark
- Tableau
- AWS
- Machine Learning
- Big Data Tools

*Keywords: Business Intelligence, Analytics Platform, Data Visualization, Predictive Analytics*",
                'type' => 'project'
            ],
            [
                'img' => '/files/Dashboard.jpg',
                'title' => 'Enterprise Analytics Dashboard',
                'body' => "# Enterprise Data Visualization Platform

## Transforming Data into Insights

Our enterprise dashboard solution processes and visualizes complex data streams in real-time, serving over 500 corporate clients. The platform handles 100M+ data points daily while maintaining sub-second response times.

Custom visualization algorithms reduce data interpretation time by 65% while improving decision accuracy by 40%.

### Key Features:
- Real-time data processing
- Custom visualization tools
- Automated reporting
- Alert management
- Role-based access
- Data export tools
- Mobile compatibility

### Tech Stack:
- D3.js
- Python/FastAPI
- ClickHouse
- Grafana
- Kubernetes
- Redis
- Apache Kafka

*Keywords: Business Intelligence, Data Visualization, Enterprise Analytics, Real-time Monitoring*",
                'type' => 'project'
            ],
            [
                'img' => '/files/Email Details.jpg',
                'title' => 'Enterprise Mail Flow - Email Management System',
                'body' => "# Advanced Email Workflow Platform

## Streamlining Corporate Communications

Enterprise Mail Flow revolutionizes email management for large organizations, processing over 10 million emails monthly with intelligent routing and compliance checks.

Our system reduced email processing time by 70% while ensuring 100% compliance with industry regulations.

### Platform Features:
- Smart email routing
- Compliance checking
- Template management
- Workflow automation
- Analytics dashboard
- Archive management
- Security protocols

### Technology Stack:
- Node.js
- MongoDB
- RabbitMQ
- ElasticSearch
- Azure
- Machine Learning
- OAuth2

*Keywords: Email Management, Workflow Automation, Enterprise Communication, Compliance*",
                'type' => 'project'
            ],
            [
                'img' => '/files/fulerserb.fw.png',
                'title' => 'FullServe - Restaurant Management Platform',
                'body' => "# Complete Restaurant Management Solution

## Revolutionizing Food Service Operations

FullServe transforms restaurant operations with an integrated platform handling everything from orders to inventory. Currently serving 1000+ restaurants across multiple locations.

The system increased operational efficiency by 45% and reduced food waste by 30%.

### Core Features:
- Order management
- Inventory tracking
- Staff scheduling
- Table management
- Kitchen display system
- Customer loyalty
- Analytics suite

### Technologies Used:
- React Native
- PHP/Laravel
- PostgreSQL
- WebSockets
- AWS
- Payment Gateway
- POS Integration

*Keywords: Restaurant Management, POS System, Inventory Control, Food Service Technology*",
                'type' => 'project'
            ],
            [
                'img' => '/files/HORSE2.fw.png',
                'title' => 'Equestrian Pro - Horse Management System',
                'body' => "# Professional Equestrian Management Platform

## Digital Innovation for Equestrian Centers

Equestrian Pro modernizes horse facility management with comprehensive tracking and care coordination. The platform manages operations for 200+ equestrian centers worldwide.

Our solution improved care coordination efficiency by 55% and reduced administrative overhead by 40%.

### Platform Features:
- Horse health records
- Training schedules
- Event management
- Billing system
- Veterinary integration
- Breeding programs
- Facility planning

### Tech Stack:
- Vue.js
- Python/Django
- MySQL
- Cloud Storage
- Mobile Apps
- GPS Integration
- IoT Sensors

*Keywords: Equestrian Management, Animal Care, Facility Management, Event Planning*",
                'type' => 'project'
            ]
        ];


        foreach ($projects as $projectData) {
            $project = new Project();
            $project->setImg($projectData['img']);
            $project->setTitle($projectData['title']);
            $project->setBody($projectData['body']);
            $project->setType($projectData['type']);

            if (!$project->save()) {
                echo "Failed to save project: " . $project->getTitle() . "\n";
                foreach ($project->getMessages() as $message) {
                    echo $message->getMessage() . "\n";
                }
            } else {
                echo "Successfully created project: " . $project->getTitle() . "\n";
            }
        }
    }

}