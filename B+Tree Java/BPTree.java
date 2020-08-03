import java.util.LinkedList;
import java.util.Queue;

public class BPTree {
    public int g_maxKeys;
    public int g_maxChild;
    public BPNode m_rootNode; 

    protected class BPNode {
        // list of keys
        public int[] m_keys;
        // list of child nodes
        public BPNode[] m_children;
        // only applicable for leaves
        public String[] m_values;
        public BPNode m_parent;
        public int m_childCount;
        public int m_keyCount;
        public BPNode m_linkedNode;

        public boolean isLeaf() {
            return m_childCount == 0;
        }
        public boolean isRoot() {
            if(m_parent == null && m_rootNode != this) {
                System.out.println("no parent, but isn't recorded as root");
            }
            return m_parent == null;
        }

        public BPNode(int key) {
            m_keys = new int[g_maxKeys + 1];
            m_children = new BPNode[g_maxChild + 1];
            m_childCount = 0;
            m_keyCount = 0;

            m_keys[m_keyCount] = key;
            m_keyCount++;
        }

        public BPNode() {
            m_keys = new int[g_maxKeys + 1];
            m_children = new BPNode[g_maxChild + 1];
            m_childCount = 0;
            m_keyCount = 0;
        }

        public boolean keyOverflow() {
            return m_keyCount > g_maxKeys;
        }

        // Returns true on successful delete, false on failure
        public boolean deleteKey(int key) {
            BPNode node = searchKey(key);
            if(node == null) {
                return false;
            }


            return true;

        }

        // Returns true on a successful insert, and false on failure
        public boolean insertKey(int key) {
            if(!isLeaf()) {
                BPNode insertTo = searchNode(key);
                if(insertTo == null) {
                    System.out.println("Insert key " + String.valueOf(key) + " into: null for some reason");
                } else {
                    System.out.println("Insert key " + String.valueOf(key) + " into: " + insertTo.toString());
                }

                if(insertTo == null) {
                    return false;
                }

                return insertTo.insertKey(key);
            }

            int insertIdx = sortedInsert(key);

            // If no overflow, return successfully
            if(!keyOverflow()) {
                return true;
            }
            System.out.println(String.valueOf(key) + " overflows");

            splitNode();

            return true;
        }


        protected BPNode splitNode() {
            // Create new node
            BPNode newNode = new BPNode();

            // Set parent, and linked node
            newNode.m_parent = m_parent;
            newNode.m_linkedNode = m_linkedNode;
            m_linkedNode = newNode;
            
            // Create index to parse newNode's keys and children
            int idx = 0;

            // If not a leaf node, then don't copy over the middle key, it will be propagated upward
            int keyStartIdx = getSplitIdx();
            // Store the key that will be used to insert to the parent
            int insertKey = m_keys[(g_maxKeys + 1)/2];

            boolean isLeaf = isLeaf();
            // Copy keys and children
            for(int i = keyStartIdx; i <= g_maxKeys; i++) {
                newNode.m_keys[idx] = m_keys[i];
                newNode.m_children[idx] = m_children[i + 1];

                // increment newNode key index
                idx++;
                // Reduce # of keys each time a key is transferred to newNode
                m_keyCount--;
                
                if(!isLeaf) {
                    m_childCount--;
                    newNode.m_childCount++;
                }
            }
            // Add last child to the newNode
            if(!isLeaf) {
                newNode.m_children[idx] = m_children[g_maxKeys + 1];
                newNode.m_childCount++;
            }

            // Set newNode's key count
            newNode.m_keyCount = idx;

            if(!isLeaf) {
                m_keyCount--;
                m_childCount--;
            }

            if(isRoot()) {
                BPNode[] arr = {this, newNode};
                return createParent(arr, insertKey);
            }

            return m_parent.insertChild(newNode, insertKey);
        }

        // Creates a parent to split into
        public BPNode createParent(BPNode[] children, int insertKey) {
            m_rootNode = new BPNode(insertKey);
            for(int i = 0; i < children.length; i++) {
                m_rootNode.m_children[i] = children[i];
                m_rootNode.m_childCount++;

                children[i].m_parent = m_rootNode;
            }
            return m_rootNode;
        }

        // Inserts a child at a given index
        public BPNode insertChild(BPNode newChild, int insertKey) {
            newChild.m_parent = this;
            int insertIdx = sortedInsert(insertKey);

            m_children[insertIdx] = newChild;
            m_childCount++;

            if(keyOverflow()) {
                return splitNode();
            }

            return this;
        }

        // Get the index at which the split node will start
        protected int getSplitIdx() {
            return isLeaf() ? (g_maxKeys + 1)/2 : (g_maxKeys + 1)/2 + 1;
        }

        // Inserts new key and appropriately increments keyCount, and sorts m_keys, returns the key's index
        private int sortedInsert(int key) {
            int insertIdx = m_keyCount;
            // Find where the key should be inserted
            for(int i = 0; i < m_keyCount; i++) {
                if(m_keys[i] == key) {
                    return i;
                } else if(m_keys[i] > key) {
                    insertIdx = i;
                    break;
                }
            }

            // shift keys to make room for the new key
            for(int i = m_keyCount; i > insertIdx; i--) {
                m_keys[i] = m_keys[i - 1];
            }

            // insert new key
            m_keys[insertIdx] = key;
            m_keyCount++;

            // if not a leaf node shift children as well to make room for new child insert
            // NOTE: Does not increment child count. Caller function needs to take care of insertion
            if(!isLeaf()) {
                for(int i = m_childCount - 1; i > insertIdx; i--) {
                    m_children[i + 1] = m_children[i];
                }
            }

            return insertIdx + 1;
        }

        // Searches a key's node in a tree. If found, return null, and if not found, return the node it could be added to
        public BPNode searchNode(int key) {
            if(isLeaf()) {
                for(int i = 0; i < m_keyCount; i++) {
                    if(m_keys[i] == key) {
                        System.out.println("Insert key " + String.valueOf(key) + " found in " + this.toString());
                        
                        return null;
                    }
                }

                // If key is not present, return null
                return this;
            }

            for(int i = 0; i <= m_keyCount; i++) {
                if(i == m_keyCount || m_keys[i] > key) {
                    return m_children[i].searchNode(key);
                } 
            }

            return m_children[m_childCount - 1].searchKey(key);
        }
        
        // Searches a key in the tree, and if the key is found, returns the leaf node that contains it
        public BPNode searchKey(int key) {
            if(isLeaf()) {
                for(int i = 0; i < m_keyCount; i++) {
                    if(m_keys[i] == key) {
                        return this;
                    }
                }

                // If key is not present, return null
                return null;
            }

            for(int i = 0; i < m_keyCount; i++) {
                if(m_keys[i] > key) {
                    return m_children[i].searchKey(key);
                } 
            }

            return m_children[m_childCount - 1].searchKey(key);
        } 



        // Returns the string representation of this B+ Tree node.
        @Override
        public String toString() { 
            String retStr = "/// ";
            for(int i = 0; i < m_keyCount; i++) {
                retStr += String.valueOf(m_keys[i]) + ", ";
            }
            retStr += " | ";
            retStr += " childCount : " + String.valueOf(m_childCount) + ", keyCount : " + String.valueOf(m_keyCount) + " /// ";
            return retStr; 
        } 
    }

    // Creates a new BPTree, and a root node with a key
    public BPTree(int key, int maxKeys) {
        g_maxKeys = maxKeys;
        g_maxChild = maxKeys + 1;

        m_rootNode = new BPNode(key);
    }

    // Creates a new BPTree, and a root node with a key
    public BPTree(int maxKeys) {
        g_maxKeys = maxKeys;
        g_maxChild = maxKeys + 1;
    }

    public void InsertKey(int key) {
        if(m_rootNode == null) {
            m_rootNode = new BPNode(key);
        } else {
            m_rootNode.insertKey(key);
        }
    }

    // Prints tree
    public String printTree() {
        String tree = "";

        Queue<BPNode> printQueue = new LinkedList<BPNode>();
        Queue<Integer> printQueueLevel = new LinkedList<Integer>();

        printQueue.add(m_rootNode);
        printQueueLevel.add(0);

        int lastPrintedLevel = 0;

        while(!printQueue.isEmpty()) {
            BPNode curNode = printQueue.remove();
            int curLevel = printQueueLevel.remove();

            for(int i = 0; i < curNode.m_childCount; i++) {
                if(curNode.m_children[i] != null) {
                    printQueue.add(curNode.m_children[i]);
                    printQueueLevel.add(curLevel + 1);
                }
            }
            if(curLevel > lastPrintedLevel) {
                tree += String.valueOf(lastPrintedLevel);
                tree += "\n";
                lastPrintedLevel = curLevel;
            }

            tree += curNode.toString();

        }
        tree += String.valueOf(lastPrintedLevel);
        tree += "\n";
        return tree;
    } 

    @Override
    public String toString() { 
        String ret = "Key Capacity: " + String.valueOf(g_maxKeys) + ", Child Capacity: " + String.valueOf(g_maxChild) + "\n";
        if(m_rootNode != null)
            ret += m_rootNode.toString();
        return ret;
    } 
}